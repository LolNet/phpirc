<?php
/**
 * Config manager
 *
 * @author Gussi <gussi@gussi.is>
 */

class config {
    private $config = [];

    const FIELD_INT = 'field_int';
    const FIELD_ARRAY = 'field_array';
    const FIELD_STRING = 'field_string';
    const FIELD_BOOL = 'field_bool';

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function create() {
        // Set base default config fields
        $this->parse_fields(self::config_fields());

        // Go through each module
        foreach (glob('module/*.class.php') as $file) {
            // Get module class name
            $file_name = pathinfo($file, PATHINFO_BASENAME);
            list($module_name) = explode('.', $file_name, 2);
            $module_class_name = "module_{$module_name}";

            // Check if modules should be enabled
            $enabled = $this->config["module.$module_name"] = $this->field_bool([
                'name'          => "Enable $module_name?",
                'default'       => FALSE,
            ]);
            if (!$enabled) {
                // Skip module config
                continue;
            }

            // Check if module has config values
            if (is_callable("$module_class_name::config_fields")) {
                $this->parse_fields($module_class_name::config_fields(), "module.$module_name.");
            }
        }

        print("We're done :)\n");
    }

    public function save($filename) {
        return file_put_contents($filename, $this->save_pretty());
    }

    private function save_serialized() {
        return sprintf("<?php \$config = unserialize('%s');"
            , addcslashes(serialize($this->config), "'")
        );
    }

    private function save_pretty() {
        $output = "<?php\n\$config = [\n";

        foreach ($this->config as $key => $val) {
            if (is_array($val)) {
                foreach ($val as &$esc) {
                    $esc = addcslashes($esc, "'");
                }
                $output .= sprintf("    %-32s=> ['%s'],\n"
                    , "'$key'"
                    , join("','", $val)
                );
            } else if(is_bool($val)) {
                $output .= sprintf("    %-32s=> %s,\n"
                    , "'$key'"
                    , $val
                        ? 'TRUE'
                        : 'FALSE'
                );
            } else {
                $output .= sprintf("    %-32s=> '%s',\n"
                    , "'$key'"
                    , $val
                );
            }
        }

        return $output . "];\n";
    }

    public function parse_fields($config_fields, $config_prefix = '') {
        foreach ($config_fields as $field_name => $field_info) {
            // Get config value from user
            while (TRUE) {
                $value = call_user_func_array([$this, $field_info['type']], [&$field_info]);
                if (empty($field_info['validate'])) {
                    break;
                }
                if (($ret = $this->validate_parse($value, $field_info['validate'])) === TRUE) {
                    break;
                }
                printf("Invalid value '%s' for %s (%s): %s\n"
                    , $value
                    , $field_info['name']
                    , $field_name
                    , $ret
                );
            }
            $this->config[$config_prefix . $field_name] = $value;
        }
    }

    private function validate_parse($value, $validate) {
        if (is_array($value)) {
            return TRUE; # TODO: Array validation
        }
        foreach ($validate as $callback) {
            $ret = $callback($value);
            if ($ret !== TRUE) {
                return $ret ?: 'Unknown validation error';
            }
        }
        return TRUE;
    }

    private function field_string($field_info) {
        // Prompt setup
        $prompt = $field_info['name'];
        if (!empty($field_info['default'])) {
            $prompt .= " [{$field_info['default']}]";
        }
        $prompt .= ": ";
        return readline($prompt) ?: $field_info['default'];
    }

    private function field_int($field_info) {
        // Append numeric value check
        $field_info['validate'][] = function($value) {
            return is_numeric($value)
                ? TRUE
                : "Value isn't a number";
        };
        return $this->field_string($field_info);
    }

    private function field_array($field_info) {
        $ret = [];
        $question = $field_info['name'] . ' (Multiple values, empty value terminates):';
        print($question);
        while (TRUE) {
            $value = readline();
            if (empty($value)) {
                break;
            }
            $ret[] = $value;
        }
        return $ret;
    }

    private function field_bool($field_info) {
        $question = $field_info['name'];
        $default = $field_info['default'];

        // Append default value
        if ($default) {
            $question .= ' [Y/n] ';
        } else {
            $question .= ' [y/N] ';
        }

        $ret = NULL;
        while (TRUE) {
            $answer = readline($question);
            switch ($answer) {
                case 'Y':
                case 'y':
                case 'YES':
                case 'yes':
                    $ret = TRUE;
                    break 2;
                case 'N':
                case 'n':
                case 'NO':
                case 'no':
                    $ret = FALSE;
                    break 2;
                case '':
                    $ret = $default;
                    break 2;
                default:
                    print("Invalid answer, pick yes or no\n");
                    break;
            }
        }

        return $ret;
    }

    static public function config_fields() {
        return [
            'server.host'       => [
                'name'              => 'IRC server hostname',
                'type'              => config::FIELD_STRING,
                'default'           => 'irc.lolnet.is',
            ],
            'server.port'       => [
                'name'              => 'IRC server port',
                'type'              => config::FIELD_INT,
                'default'           => '6668',
                'validate'          => [
                    function($value) {
                        return (0x0 < $value && $value < 0xFFFF)
                            ? TRUE
                            : 'Invalid port number';
                    },
                ],
            ],
            'server.nick'       => [
                'name'              => 'Nickname',
                'type'              => config::FIELD_STRING,
                'default'           => 'phpirc_' . uniqid(),
            ],
            'server.user'       => [
                'name'              => 'Username',
                'type'              => config::FIELD_STRING,
                'default'           => 'phpirc',
            ],
            'server.real'       => [
                'name'              => 'Real name',
                'type'              => config::FIELD_STRING,
                'default'           => 'LolNet/phpirc',
            ],
        ];
    }

    public function get_config() {
        return $this->config;
    }

    public function get($key, $default = NULL) {
        return isset($this->config[$key])
            ? $this->config[$key]
            : NULL;
    }

    public function set($key, $value) {
        $this->config[$key] = $value;
    }
}
