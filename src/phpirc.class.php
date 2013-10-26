<?php
/**
 * phpirc
 *
 * @author Gussi <gussi@gussi.is>
 */

class phpirc {
    private $sock;
    private $log;
    public $config;

    private $buffer = [];
    private $modules = [];

    /**
     * Create instance of phpirc
     *
     * @param array $config                 Config array
     *  @li string $server                      Server address
     *  @li int $port                           Server port
     *  @li string $pass                        Server password
     *  @li string $nick                        Client nick
     *  @li string $user                        Client user
     *  @li string $realname                    Client real name
     *  @li bool $echo                          Echo all traffic
     */
    public function __construct($config) {
        $this->log = new log('phpirc');
        $this->log->info("phpirc started");
        $this->config = $config;

        foreach ($config['module'] as $module_name => $conf) {
            if ($conf === FALSE) {
                continue;
            }
            $this->module_register($module_name);
        }

        $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($this->sock, $config['server']['host'], $config['server']['port']);
        if (!empty($this->config['server']['pass'])) {
            $this->_send_data(irc::PASS($config['server']['pass']));
        }
        $this->send(irc::NICK($config['server']['nick']));
        $this->send(irc::USER($config['server']['user'], 0, '*', ($config['server']['real'] ?: $config['server']['nick'])));
        socket_set_nonblock($this->sock);
    }

    public function process() {
        if ($data = $this->recv_data()) {
            if (isset($this->config['server']['echo']) && $this->config['server']['echo'] === TRUE) {
                $this->log->debug("RECV : %s", $data);
            }

            foreach ($this->modules as $module) {
                $module->process($data);
            }

            return TRUE;
        }

        foreach ($this->modules as $module) {
            $module->process($data);
        }

        return FALSE;
    }

    private function recv_data() {
        if (count($this->buffer) > 0) {
            return array_shift($this->buffer);
        }
        $buffer = '';
        do {
            $chunk = socket_read($this->sock, 8192);
            if ($chunk === false) {
                $error = socket_last_error($this->sock);
                if ($error != 11 && $error != 115 && $error != 35) {
                    throw new Exception(printf("ERR: Disconnected (errno %s)\n", $error));
                    return FALSE;
                }
                break;
            } elseif ($chunk == '') {
                throw new Exception(printf("ERR: Disconnected (errno %s)\n", $error));
                return FALSE;
                break;
            } else {
                $buffer .= $chunk;
            }
        } while (true);
        foreach (explode("\r\n", $buffer) as $msg) {
            if (!empty($msg)) {
                $this->buffer[] = $msg;
            }
        }
        return array_shift($this->buffer);
    }

    private function _send_data($data) {
        socket_write($this->sock, $data . "\r\n");
    }

    /**
     * Send data, append crlf
     *
     * @param string $data                  Data to be sent
     */
    public function send($data) {
        if (strstr($data, "\r\n") !== FALSE) {
            return;
        }
        if ($this->config['server']['echo']) {
            $this->log->debug("SEND : %s", $data);
        }
        $this->_send_data($data);
    }

    public function config() {
        return $this->config;
    }

    /**
     * Return module instance
     *
     * @param string $module_name           Module name to return
     * @return module                       Module instance, NULL on failure
     */
    public function module($module_name) {
        if (isset($this->modules[$module_name])) {
            return $this->modules[$module_name];
        }
    }

    /**
     * Register a module
     *
     * @param string $module_name           Module to register
     * @return bool                         TRUE on success, else FALSE
     */
    public function module_register($module_name) {
        if (isset($this->modules[$module_name])) {
            return FALSE;
        }
        $class_name = "module_" . $module_name;
        if (!class_exists($class_name)) {
            return FALSE;
        }
        $module = new $class_name($this);
        if ($module->init() === FALSE) {
            return FALSE;
        }
        $this->modules[$module_name] = $module;
        return TRUE;
    }

    /**
     * Unregister module
     *
     * @param string $module_name           Module to unregister
     * @return bool                         TRUE on success, else FALSE
     */
    public function module_unregister($module_name) {
        if (!isset($this->modules[$module_name])) {
            return FALSE;
        }
        $this->modules[$module_name]->clean();
        unset($this->_module[$module_name]);
        return TRUE;
    }

}
