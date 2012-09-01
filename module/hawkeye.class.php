<?php
/**
 * HawkEye
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_minecraft_hawkeye extends module_minecraft {
	private $cursor_data_id;
	private $cursor_summary_data_id;
	private $cursor_xray_data_id;
	private $query_peak_summary = 0;
	private $query_peak_xray = 0;

	public function init() {
		$this->db = new database($this->config['db']['dsn'], $this->config['db']['user'], $this->config['db']['pass']);
		if($this->db === FALSE) {
			fprintf("ERR: [%s] unable to connect to database\n", get_class($this));
			return FALSE;
		}

		$this->cursor_data_id = $this->cursor_summary_data_id = $this->cursor_xray_data_id = $this->get_last_id();

		$this->timer(1, array($this, 'monitor'));
		$this->timer(60*10, array($this, 'summary'));
		$this->timer(60*60, array($this, 'xray'));
	}

	public function xray() {
		$last_id = $this->get_last_id();
		$t1 = microtime(true);
		$rows = $this->db->get_all('
			SELECT
				`t2`.`player`,
				COUNT(*) AS `ore_mined`,
				IFNULL(`tmp_stone`.`stone`, 1) AS `stone_mined`,
				(COUNT(*) / `tmp_stone`.`stone`) AS `luck`
			FROM `hawkeye` AS `t1`
			LEFT JOIN `hawk_players` AS `t2` ON `t2`.`player_id` = `t1`.`player_id`
			LEFT JOIN (
				SELECT
					`t3`.`player_id`,
					`t3`.`data`,
					COUNT(*) AS `stone`
				FROM `hawkeye` AS `t3`
				WHERE `t3`.`action` = 0
				AND `t3`.`data` = 1
				AND `t3`.`data_id` > ?
				GROUP BY `t3`.`data`, `t3`.`player_id`
			) AS `tmp_stone` ON (`t1`.`player_id` = `tmp_stone`.`player_id`) 
			WHERE `t1`.`action` = 0
			AND `t1`.`data` IN (14, 15, 21, 56)
			AND `t1`.`data_id` > ?
			GROUP BY `t1`.`player_id`
			HAVING `luck` > 0.25
			ORDER BY `luck` DESC
			LIMIT 100'
			, $this->cursor_xray_data_id
			, $this->cursor_xray_data_id
		);
		$t2 = microtime(true);
		$query_time = $t2-$t1;
		if($query_time > $this->query_peak_xray) {
			$this->query_peak_xray = $query_time;
			$this->parent->send(irc::PRIVMSG($this->config['channel'], sprintf("[HawkEye] [debug] Query time peak: %.3f sec", $query_time)));
		}

		foreach($rows as $row) {
			$this->parent->send(irc::PRIVMSG($this->config['channel'], sprintf("[HawkEye] [xray] %s mined %d ores vs %d stones - %f"
				, $row['player']
				, $row['ore_mined']
				, $row['stone_mined']
				, $row['luck']
			)));
		}
		$this->cursor_xray_data_id = $last_id;
	}

	public function summary() {
		$last_id = $this->get_last_id();
		$t1 = microtime(true);
		$rows = $this->db->get_all('
			SELECT
				COUNT(*) AS `count`,
				`t3`.`player`,
				`t1`.*
			FROM `hawkeye` AS `t1`
			LEFT JOIN `hawk_players` AS `t3` ON `t3`.`player_id` = `t1`.`player_id`
			WHERE `t1`.`data_id` > ?
			AND `t1`.`action` = 0
			AND `t1`.`world_id` = 1
			AND (
				SELECT `t2`.`player_id`
				FROM `hawkeye` AS `t2`
				WHERE `t2`.`action` = 1
				AND `t2`.`x` = `t1`.`x`
				AND `t2`.`y` = `t1`.`y`
				AND `t2`.`z` = `t1`.`z`
				ORDER BY `data_id` DESC
				LIMIT 1
			) != `t1`.`player_id`
			GROUP BY `t1`.`player_id`, `t1`.`data`'
			, $this->cursor_summary_data_id
		);
		$t2 = microtime(true);
		$query_time = $t2-$t1;
		if($query_time > $this->query_peak_summary) {
			$this->query_peak_summary = $query_time;
			$this->parent->send(irc::PRIVMSG($this->config['channel'], sprintf("[HawkEye] [summary] Query time peak: %.3f sec", $query_time)));
		}

		$players = array();
		foreach($rows as $row) {
			if(!isset($players[$row['player']])) {
				$players[$row['player']] = array();
			}
			$players[$row['player']][] = $row;
		}

		foreach($players as $player => $rows) {
			$items = array();
			$item_count = 0;
			$coords_arr = array();
			foreach($rows as $row) {
				$data = array_shift(explode(':', $row['data']));
				$items[] = sprintf("%d %s", $row['count'], $this->get_item_name($data));
				$item_count += $row['count'];
				$coord_current = array(
					'x'		=> $row['x'],
					'y'		=> $row['y'],
					'z'		=> $row['z'],
				);
				$coord_unique = TRUE;
				foreach($coords_arr as $coord) {
					if ($this->calc_distance($coord, $coord_current) < 64) {
						$coord_unique = FALSE;
					}
				}
				if($coord_unique) {
					$coords_arr[] = $coord_current;
				}
			}
			if($item_count < 10) {
				continue;
			}
			foreach($coords_arr as &$coord) {
				$coord = implode(' ', $coord);
			}
			$this->parent->send(irc::PRIVMSG('#gussi.is-monitor', sprintf("[HawkEye] [summary] %s var að skemma: [%s], [%s]"
				, $player
				, implode(' | ', $items)
				, implode(' | ', $coords_arr)
			)));
		}

		$this->cursor_summary_data_id = $last_id;
	}

	private function calc_distance($p1, $p2) {
		return abs(pow($p1['x'] - $p2['x'], 2) + pow($p1['y'] - $p2['y'], 2) + pow($p1['z'] - $p2['z'], 2));
	}

	public function monitor() {
		$last_id = $this->get_last_id();
		$rows = $this->db->get_all('
			SELECT
				`t1`.*,
				`hawk_players`.`player`
			FROM `hawkeye` AS `t1`
			LEFT JOIN `hawk_players` ON `hawk_players`.`player_id` = `t1`.`player_id`
			WHERE `t1`.`action` = 0
			AND `t1`.`world_id` = 1
			AND `t1`.`data` IN (41, 42, 57, 22, 89, 45, 88, 35)
			AND `t1`.`data_id` > ?
			AND `t1`.`data_id` <= ?
			AND (
				SELECT `t2`.`player_id`
				FROM `hawkeye` AS `t2`
				WHERE `t2`.`action` = 1
				AND `t2`.`x` = `t1`.`x`
				AND `t2`.`y` = `t1`.`y`
				AND `t2`.`z` = `t1`.`z`
				ORDER BY `data_id` DESC
				LIMIT 1
			) != `t1`.`player_id`
			ORDER BY `data_id` DESC'
			, $this->cursor_data_id
			, $last_id
		);
		foreach($rows as $row) {
			$this->parent->send(irc::PRIVMSG('#gussi.is-staff', sprintf("[HawkEye] %s braut %s @ [%d %d %d]"
				, $row['player']
				, self::get_item_name(array_shift(explode(':', $row['data'])))
				, $row['x']
				, $row['y']
				, $row['z']
			)));
		}

		$rows = $this->db->get_all("
			SELECT
				`t1`.*,
				`hawk_players`.`player`
			FROM `hawkeye` AS `t1`
			LEFT JOIN `hawk_players` ON `hawk_players`.`player_id` = `t1`.`player_id`
			WHERE `t1`.`action` = 4
			AND (
				`t1`.`data` REGEXP '^(/he|/hk|/hawk|/hawkeye) (rebuild|rollback|preview|werollback|apply|cancel)'
				OR `t1`.`data` LIKE '/pex %'
				OR `t1`.`data` LIKE '/dmarker %'
				OR `t1`.`data` LIKE '/tempban %'
				OR `t1`.`data` LIKE '/ban %'
			)
			AND `t1`.`data_id` > ?
			AND `t1`.`data_id` <= ?
			ORDER BY `data_id` DESC"
			, $this->cursor_data_id
			, $last_id
		);
		foreach($rows as $row) {
			$this->parent->send(irc::PRIVMSG($this->config['channel'], sprintf("[HawkEye] %s gerði '%s' @ [%d %d %d]"
				, $row['player']
				, $row['data']
				, $row['x']
				, $row['y']
				, $row['z']
			)));
		}
		$this->cursor_data_id = $last_id;
	}

	private function get_last_id() {
		return $this->db->get_field('
			SELECT `data_id`
			FROM `hawkeye`
			ORDER BY `data_id` DESC
			LIMIT 1'
		);
	}
}
