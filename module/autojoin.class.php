<?php
/**
 * Autojoin module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_autojoin extends module {
    /**
     * Config fields
     */
    static public function config_fields() {
        return [
            'channels'      => [
                'name'          => 'Channels to join',
                'type'          => config::FIELD_ARRAY,
                'validate'      => [
                    function($value) {
                        if ($value[0] != '#') {
                            return 'Invalid channel name';
                        }

                        return TRUE;
                    }
                ],
            ],
        ];
    }

    public function init() {
        // Wait for RPL_WELCOME from IRC server
        $this->event(irc::RPL_WELCOME, function($data) {
            // Wait 1 second (hardcore NickServ might be in action) then join
            $this->timer(1, [$this, 'join'], timer::TIMER_ONCE);
            return event::UNREGISTER;
        });
    }

    public function join() {
        $channels = $this->config->get('module.autojoin.channels');
        if (!empty($channels)) {
            $this->parent()->send(irc::JOIN($channels));
        } else {
            $this->log->error("No channel/s defined");
        }
    }
}
