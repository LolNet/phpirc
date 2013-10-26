<?php
/**
 * Fantasy subscription module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_fantasy extends module {
    private $commands = [];

    public function init() {
        $this->event('PRIVMSG', function($data) {
            $fantasy = irc::parse_fantasy($data);
            if (empty($fantasy)) {
                return;
            }

            if (!isset($this->commands[$fantasy['cmd']])) {
                return FALSE;
            }

            foreach ($this->commands[$fantasy['cmd']] as &$cmd) {
                $ret = call_user_func($cmd, $fantasy);
                if ($ret === FALSE) {
                    unset($cmd);
                }
            }
        });
    }

    /**
     * Register given fantasy command to a callback
     *
     * @param $command              Fantasy command
     * @param $callback             Callable function or closure
     */
    public function register($command, $callback) {
        if (!is_callable($callback)) {
            return FALSE;
        }

        if (!is_array($command)) {
            $command = [$command];
        }

        foreach ($command as $cmd) {
            if (empty($this->commands[$cmd])) {
                $this->commands[$cmd] = [];
            }
            $this->commands[$cmd][] = $callback;
        }
    }
}
