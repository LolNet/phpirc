<?php
/**
 * Ping module, answer PINGs with PONGs
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_ping extends module {
	public function init() {
		$this->event('PING', function($data) {
			list($cmd, $server) = explode(' ', $data);
			$this->parent()->send(irc::PONG(substr($server, 1)));
		});
	}
}
