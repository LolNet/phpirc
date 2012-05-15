<?php
/**
 * Ping module, answer PINGs with PONGs
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_ping extends module {
	public function init() {
		$module = $this;
		$this->event('PING', function($data) use($module) {
			list($cmd, $server) = explode(' ', $data);
			$module->parent()->send(irc::PONG(substr($server, 1)));
		});
	}
}
