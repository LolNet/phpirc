<?php
/**
 * Events for modules
 *
 * @author Gussi <gussi@gussi.is>
 */

class event {
    const UNREGISTER = 0xFF;

    /**
     * Subscribe to given event
     *
     * @param string|array event            Event or array of events
     * @param string|closure callback       Callback to call when given event happens
     */
    public function subscribe($event, $callback) {
        if (!is_array($event)) {
            $event = array($event);
        }
        foreach ($event as $val) {
            if (!isset($this->event[strval($val)])) {
                $this->event[strval($val)] = array();
            }
            $this->event[strval($val)][] = $callback;
        }
    }

    /**
     * Notify all listening callbacks for event
     *
     * @param string|int event              Event to trigger
     * @param string data                   Data recieved
     */
    public function notify($event, $data) {
        if (!isset($this->event[strval($event)])) {
            return;
        }
        foreach ($this->event[strval($event)] as &$callback) {
            $ret = call_user_func($callback, trim($data));
            switch ($ret) {
                case self::UNREGISTER:
                    unset($callback);
                    if (count($this->event[strval($event)]) == 0) {
                        unset($this->event[strval($event)]);
                    }
                    break;
            }
        }
    }
}
