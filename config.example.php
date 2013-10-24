<?php
/**
 * Example config.php
 *
 * @author Gussi <gussi@gussi.is>
 */

$config = [
	/**
	 * Common server info
	 */
	'server'			=> [
		'host'				=> 'irc.lolnet.is',
		'port'				=> '6667',
		'nick'				=> 'phpirc_' . uniqid(),
		'user'				=> 'phpirc',
		'real'				=> 'lolnet.is',
		'echo'				=> TRUE,
	],

	/**
	 * Module config
	 */
	'module'			=> [
		'ping'				=> TRUE,
		'nickchange'		=> TRUE,
		'autojoin'			=> [
			'#phpirc',
		],
	],
];
