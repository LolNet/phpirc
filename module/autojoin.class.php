<?php
/**
 * Autojoin module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_autojoin extends module {
	public function init() {
		$module = $this;
		// Wait for RPL_WELCOME from IRC server
		$this->event(irc::RPL_WELCOME, function($data) use ($module) {
			// Wait 1 second (hardcore NickServ might be in action) then join
			$module->timer(1, array($module, 'join'), timer::TIMER_ONCE);
			return event::UNREGISTER;
		});
	}

	public function join() {
		if(!empty($this->config)) {
			if(is_array($this->config)) {
				$channels = $this->config;
			} else {
				$channels = array($this->config);
			}
			$this->parent()->send(irc::JOIN($channels));
		} else{
			printf("ERR: [%s] No channel/s defined\n", get_class($this));
		}
	}
}
