<?php
/**
 * Nickchange module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_nickchange extends module {
	public function init() {
		$module = $this;
		$this->event(array(
			irc::ERR_NICKNAMEINUSE,
			irc::ERR_RESTRICTED,
			irc::ERR_NICKCOLLISION,
		), function($data) use($module) {
			$module->send(irc::NICK($module->parent->config['server']['nick'] . rand(1000, 9999)));
		});
	}
}
