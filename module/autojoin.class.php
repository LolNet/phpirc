<?php
/**
 * Autojoin module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_autojoin extends module {
	public function init() {
		// Wait for RPL_WELCOME from IRC server
		$this->event(irc::RPL_WELCOME, function($data) {
			// Wait 1 second (hardcore NickServ might be in action) then join
			$this->timer(1, [$this, 'join'], timer::TIMER_ONCE);
			return event::UNREGISTER;
		});
	}

	public function join() {
		if (!empty($this->config)) {
			if (is_array($this->config)) {
				$channels = $this->config;
			} else {
				$channels = [$this->config];
			}
			$this->parent()->send(irc::JOIN($channels));
		} else{
			printf("ERR: [%s] No channel/s defined\n", get_class($this));
		}
	}
}
