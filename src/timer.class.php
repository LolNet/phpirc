<?php
/**
 * Timer
 *
 * @author Gussi <gussi@gussi.is>
 */

class timer {
    private $timers = [];

    const TIMER_INTERVAL = 0x01;
    const TIMER_ONCE = 0x02;

    const UNREGISTER = 0xFF;

    /**
     * Start a new timer
     *
     * @param int seconds                   Seconds untill callback is triggered (or interval)
     * @param array|string|closure callback Callback to exec on trigger
     * @param int type                      Timer type, see self::TIMER_* const, INTERVAL for default
     */
    public function start($seconds, $callback, $type = self::TIMER_INTERVAL) {
        if (!is_callable($callback) || !is_int($seconds)) {
            return FALSE;
        }
        $this->timers[] = [
            'trigger'       => time() + $seconds,
            'callback'      => $callback,
            'type'          => $type,
            'seconds'       => $seconds,
        ];
        return TRUE;
    }

    /**
     * Check on timers, triggered passed timers
     */
    public function tick() {
        foreach ($this->timers as $key => &$timer) {
            if ($timer['trigger'] < time()) {
                $ret = call_user_func($timer['callback']);
                if ($ret === self::UNREGISTER || $timer['type'] === self::TIMER_ONCE) {
                    unset($this->timers[$key]);
                }
                if ($timer['type'] === self::TIMER_INTERVAL) {
                    $timer['trigger'] = time() + $timer['seconds'];
                }
            }
        }
    }
}
