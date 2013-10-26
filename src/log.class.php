<?php
/**
 * Centralized logging
 *
 * @author Gussi <gussi@gussi.is>
 */

class log {
    private $prefix;
    private $output;

    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';

    public function __construct($prefix = 'generic', $output = STDOUT) {
        $this->prefix = $prefix;
        $this->output = $output;
    }

    public function __call($method, $args) {
        if (!in_array($method, $this->get_levels())) {
            return FALSE;
        }

        $message = array_shift($args);

        fprintf($this->output, "[%s] [%-8s] %s : %s\n"
            , date('Y-m-d H:i:s')
            , strtoupper($method)
            , strtoupper($this->prefix)
            , vsprintf($message, $args)
        );
    }

    private function get_levels() {
        return [
            self::LEVEL_DEBUG,
            self::LEVEL_INFO,
            self::LEVEL_WARNING,
            self::LEVEL_ERROR,
            self::LEVEL_CRITICAL,
        ];
    }
}
