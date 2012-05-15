<?php
/**
 * Nickserv module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_nickserv extends module {
	public function init() {
		$module = $this;
		$this->event(irc::RPL_WELCOME, function($data) use ($module) {
			$module->parent()->send(irc::PRIVMSG('NickServ', "identify {$module->config['password']}"));
		});
	}
}
