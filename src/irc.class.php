<?php
/**
 * RPL and ERR constants with helper functions for IRC
 *
 * @author Gussi <gussi@gussi.is>
 */

final class irc {
	const RPL_WELCOME = '001';
	const RPL_YOURHOST = '002';
	const RPL_CREATED = '003';
	const RPL_MYINFO = '004';
	const RPL_BOUNCE = '005';
	const RPL_USERHOST = 302;
	const RPL_ISON = 303;
	const RPL_AWAY = 301;
	const RPL_UNAWAY = 305;
	const RPL_NOWAWAY = 306;
	const RPL_WHOISUSER = 311;
	const RPL_WHOISSERVER = 312;
	const RPL_WHOISOPERATOR = 313;
	const RPL_WHOISIDLE = 317;
	const RPL_ENDOFWHOIS = 318;
	const RPL_WHOISCHANNELS = 319;
	const RPL_WHOWASUSER = 314;
	const RPL_ENDOFWHOWAS = 369;
	const RPL_LISTSTART = 321;
	const RPL_LIST = 322;
	const RPL_LISTEND = 323;
	const RPL_UNIQOPIS = 325;
	const RPL_CHANNELMODEIS = 324;
	const RPL_NOTOPIC = 331;
	const RPL_TOPIC = 332;
	const RPL_INVITING = 341;
	const RPL_SUMMONING = 342;
	const RPL_INVITELIST = 346;
	const RPL_ENDOFINVITELIST = 347;
	const RPL_EXCEPTLIST = 348;
	const RPL_ENDOFEXCEPTLIST = 349;
	const RPL_VERSION = 351;
	const RPL_WHOREPLY = 352;
	const RPL_ENDOFWHO = 315;
	const RPL_NAMREPLY = 353;
	const RPL_ENDOFNAMES = 366;
	const RPL_LINKS = 364;
	const RPL_ENDOFLINKS = 365;
	const RPL_BANLIST = 367;
	const RPL_ENDOFBANLIST = 368;
	const RPL_INFO = 371;
	const RPL_ENDOFINFO = 374;
	const RPL_MOTDSTART = 375;
	const RPL_MOTD = 372;
	const RPL_ENDOFMOTD = 376;
	const RPL_YOUREOPER = 381;
	const RPL_REHASHING = 382;
	const RPL_YOURESERVICE = 383;
	const RPL_TIME = 391;
	const RPL_USERSSTART = 392;
	const RPL_USERS = 393;
	const RPL_ENDOFUSERS = 394;
	const RPL_NOUSERS = 395;
	const RPL_TRACELINK = 200;
	const RPL_TRACECONNECTING = 201;
	const RPL_TRACEHANDSHAKE = 202;
	const RPL_TRACEUNKNOWN = 203;
	const RPL_TRACEOPERATOR = 204;
	const RPL_TRACEUSER = 205;
	const RPL_TRACESERVER = 206;
	const RPL_TRACESERVICE = 207;
	const RPL_TRACENEWTYPE = 208;
	const RPL_TRACECLASS = 209;
	const RPL_TRACERECONNECT = 210;
	const RPL_TRACELOG = 261;
	const RPL_TRACEEND = 262;
	const RPL_STATSLINKINFO = 211;
	const RPL_STATSCOMMANDS = 212;
	const RPL_ENDOFSTATS = 219;
	const RPL_STATSUPTIME = 242;
	const RPL_STATSOLINE = 243;
	const RPL_UMODEIS = 221;
	const RPL_SERVLIST = 234;
	const RPL_SERVLISTEND = 235;
	const RPL_LUSERCLIENT = 251;
	const RPL_LUSEROP = 252;
	const RPL_LUSERUNKNOWN = 253;
	const RPL_LUSERCHANNELS = 254;
	const RPL_LUSERME = 255;
	const RPL_ADMINME = 256;
	const RPL_ADMINLOC1 = 257;
	const RPL_ADMINLOC2 = 258;
	const RPL_ADMINEMAIL = 259;
	const RPL_TRYAGAIN = 263;
	const ERR_NOSUCHNICK = 401;
	const ERR_NOSUCHSERVER = 402;
	const ERR_NOSUCHCHANNEL = 403;
	const ERR_CANNOTSENDTOCHAN = 404;
	const ERR_TOOMANYCHANNELS = 405;
	const ERR_WASNOSUCHNICK = 406;
	const ERR_TOOMANYTARGETS = 407;
	const ERR_NOSUCHSERVICE = 408;
	const ERR_NOORIGIN = 409;
	const ERR_NORECIPIENT = 411;
	const ERR_NOTEXTTOSEND = 412;
	const ERR_NOTOPLEVEL = 413;
	const ERR_WILDTOPLEVEL = 414;
	const ERR_BADMASK = 415;
	const ERR_UNKNOWNCOMMAND = 421;
	const ERR_NOMOTD = 422;
	const ERR_NOADMININFO = 423;
	const ERR_FILEERROR = 424;
	const ERR_NONICKNAMEGIVEN = 431;
	const ERR_ERRONEUSNICKNAME = 432;
	const ERR_NICKNAMEINUSE = 433;
	const ERR_NICKCOLLISION = 436;
	const ERR_UNAVAILRESOURCE = 437;
	const ERR_USERNOTINCHANNEL = 441;
	const ERR_NOTONCHANNEL = 442;
	const ERR_USERONCHANNEL = 443;
	const ERR_NOLOGIN = 444;
	const ERR_SUMMONDISABLED = 445;
	const ERR_USERSDISABLED = 446;
	const ERR_NOTREGISTERED = 451;
	const ERR_NEEDMOREPARAMS = 461;
	const ERR_ALREADYREGISTRED = 462;
	const ERR_NOPERMFORHOST = 463;
	const ERR_PASSWDMISMATCH = 464;
	const ERR_YOUREBANNEDCREEP = 465;
	const ERR_YOUWILLBEBANNED = 466;
	const ERR_KEYSET = 467;
	const ERR_CHANNELISFULL = 471;
	const ERR_UNKNOWNMODE = 472;
	const ERR_INVITEONLYCHAN = 473;
	const ERR_BANNEDFROMCHAN = 474;
	const ERR_BADCHANNELKEY = 475;
	const ERR_BADCHANMASK = 476;
	const ERR_NOCHANMODES = 477;
	const ERR_BANLISTFULL = 478;
	const ERR_NOPRIVILEGES = 481;
	const ERR_CHANOPRIVSNEEDED = 482;
	const ERR_CANTKILLSERVER = 483;
	const ERR_RESTRICTED = 484;
	const ERR_UNIQOPPRIVSNEEDED = 485;
	const ERR_NOOPERHOST = 491;
	const ERR_UMODEUNKNOWNFLAG = 501;
	const ERR_USERSDONTMATCH = 502;

	const COLOR_WHITE = 0;
	const COLOR_BLACK = 1;
	const COLOR_DARK_BLUE = 2;
	const COLOR_DARK_GREEN = 3;
	const COLOR_RED = 4;
	const COLOR_BROWN = 5;
	const COLOR_MAGENTA = 6;
	const COLOR_ORANGE = 7;
	const COLOR_YELLOW = 8;
	const COLOR_GREEN = 9;
	const COLOR_CYAN = 10;
	const COLOR_LIGHT_BLUE = 11;
	const COLOR_BLUE = 12;
	const COLOR_DARK_GRAY = 13;
	const COLOR_GRAY = 14;
	const COLOR_LIGHT_GRAY = 15;

	const COLOR_BOLD = 0x2;
	const COLOR_START = 0x3;
	const COLOR_STOP = 0xF;

	public static function COLOR($foreground = NULL, $background = NULL) {
		if ($foreground == self::COLOR_BOLD) {
			return sprintf("%s", chr(self::COLOR_BOLD));
		}

		if (is_null($foreground) && is_null($background)) {
			return sprintf("%s", chr(self::COLOR_STOP));
		}

		if (is_null($background)) {
			return sprintf("%s%02s", chr(self::COLOR_START), $foreground);
		} else {
			return sprintf("%s%02s,%02s", chr(self::COLOR_START), $foreground, $background);
		}
	}

	public static function PASS($password) {
		return sprintf("PASS %s", $password);
	}

	public static function NICK($nickname) {
		return sprintf("NICK %s", $nickname);
	}

	public static function USER($user, $mode, $unused, $realname) {
		return sprintf('USER %s %d %s :%s', $user, $mode, $unused, $realname);
	}

	public static function PONG($server) {
		if (is_array($server)) {
			$server = implode(' ', $server);
		}
		return sprintf('PONG %s', $server);
	}

	public static function JOIN($channel, $key = '') {
		if (is_array($channel)) {
			$channel = implode(',', $channel);
		}
		if (is_array($key)) {
			$key = implode(',', $key);
		}
		return sprintf('JOIN %s %s', $channel, $key);
	}

	public static function PART($channel, $message = '') {
		if (is_array($channel)) {
			$channel = implode(',', $channel);
		}
		return sprintf('PART %s :%s', $channel, $message);
	}

	public static function MODE($channel, $modes, $modeparam) {
		if (is_array($modes)) {
			$modes = implode('', $modes);
		}
		if (is_array($modeparam)) {
			$modeparam = implode(' ', $modeparam);
		}
		return sprintf('MODE %s %s %s', $channel, $modes, $modeparam);
	}

	public static function PRIVMSG($msgtarget, $message) {
		$args = array_splice(func_get_args(), 2);
		return sprintf('PRIVMSG %s :%s', $msgtarget, vsprintf($message, $args));
	}

	public static function parse_privmsg($data) {
		list($from, $cmd, $to, $message) = explode(' ', $data, 4);
		list($nick, $rest) = explode('!', substr($from, 1), 2);
		list($user, $host) = explode('@', $rest, 2);
		$message = trim(substr($message, 1));
		return [
			'nick'		=> $nick,
			'user'		=> $user,
			'host'		=> $host,
			'to'		=> $to,
			'msg'		=> $message,
			'reply'		=> (substr($to, 0, 1) == '#')
				? $to
				: $nick
		];
	}

	public static function parse_fantasy($data) {
		$msg = self::parse_privmsg($data);
		if (substr($msg['msg'], 0, 1) != '!') {
			return [];
		}

		if (strstr($msg['msg'], ' ') !== FALSE) {
			list($cmd, $args) = explode(' ', $msg['msg'], 2);
		} else {
			$cmd = $msg['msg'];
			$args = '';
		}

		return array_merge($msg, [
			'cmd'		=> substr($cmd, 1),
			'args'		=> $args,
		]);
	}
}
