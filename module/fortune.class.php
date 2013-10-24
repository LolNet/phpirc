<?php
/**
 * Fortune module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_fortune extends module {
	public function init() {
		if (`which fortune` == NULL) {
			printf("Fortune missing");
			return;
		}

		$this->event('PRIVMSG', function($data) {
			$info = irc::parse_fantasy($data);
			if (empty($info) || $info['cmd'] != 'fortune') {
				return;
			}

			$this->parent()->send(irc::PRIVMSG($info['to'], str_replace("\n", ' ', `fortune -s`)));
		});
	}
}
