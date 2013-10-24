<?php
/**
 * Nickchange module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_nickchange extends module {
	public function init() {
		$this->event([
			irc::ERR_NICKNAMEINUSE,
			irc::ERR_RESTRICTED,
			irc::ERR_NICKCOLLISION,
		], function($data) {
			$this->send(irc::NICK($this->parent->config['server']['nick'] . rand(1000, 9999)));
		});
	}
}
