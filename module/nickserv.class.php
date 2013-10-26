<?php
/**
 * Nickserv module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_nickserv extends module {
    public function init() {
        $this->event(irc::RPL_WELCOME, function($data) {
            $this->parent()->send(irc::PRIVMSG('NickServ', "identify {$this->config['password']}"));
        });
    }
}
