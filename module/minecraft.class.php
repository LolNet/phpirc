<?php
/**
 * Minecraft helper class
 *
 * @author Gussi <gussi@gussi.is>
 */

abstract class module_minecraft extends module {
	private $cache_items = array();

	// Blocks
	const AIR = 0;
	const STONE = 1;
	const GRASS = 2;
	const DIRT = 3;
	const COBBLESTONE = 4;
	const WOOD = 5;
	const SAPLING = 6;
	const BEDROCK = 7;
	const WATER = 8;
	const STATIONARY_WATER = 9;
	const LAVA = 10;
	const STATIONARY_LAVA = 11;
	const SAND = 12;
	const GRAVEL = 13;
	const GOLD_ORE = 14;
	const IRON_ORE = 15;
	const COAL_ORE = 16;
	const LOG = 17;
	const LEAVES = 18;
	const SPONGE = 19;
	const GLASS = 20;
	const LAPIS_LAZULI_ORE = 21;
	const LAPIS_LAZULI_BLOCK = 22;
	const DISPENSER = 23;
	const SANDSTONE = 24;
	const NOTE_BLOCK = 25;
	const BED = 26;
	const POWERED_RAIL = 27;
	const DETECTOR_RAIL = 28;
	const PISTON_STICKY_BASE = 29;
	const WEB = 30;
	const LONG_GRASS = 31;
	const DEAD_BUSH = 32;
	const PISTON_BASE = 33;
	const PISTON_EXTENSION = 34;
	const CLOTH = 35;
	const PISTON_MOVING_PIECE = 36;
	const YELLOW_FLOWER = 37;
	const RED_FLOWER = 38;
	const BROWN_MUSHROOM = 39;
	const RED_MUSHROOM = 40;
	const GOLD_BLOCK = 41;
	const IRON_BLOCK = 42;
	const DOUBLE_STEP = 43;
	const STEP = 44;
	const BRICK = 45;
	const TNT = 46;
	const BOOKCASE = 47;
	const MOSSY_COBBLESTONE = 48;
	const OBSIDIAN = 49;
	const TORCH = 50;
	const FIRE = 51;
	const MOB_SPAWNER = 52;
	const WOODEN_STAIRS = 53;
	const CHEST = 54;
	const REDSTONE_WIRE = 55;
	const DIAMOND_ORE = 56;
	const DIAMOND_BLOCK = 57;
	const WORKBENCH = 58;
	const CROPS = 59;
	const SOIL = 60;
	const FURNACE = 61;
	const BURNING_FURNACE = 62;
	const SIGN_POST = 63;
	const WOODEN_DOOR = 64;
	const LADDER = 65;
	const MINECART_TRACKS = 66;
	const COBBLESTONE_STAIRS = 67;
	const WALL_SIGN = 68;
	const LEVER = 69;
	const STONE_PRESSURE_PLATE = 70;
	const IRON_DOOR = 71;
	const WOODEN_PRESSURE_PLATE = 72;
	const REDSTONE_ORE = 73;
	const GLOWING_REDSTONE_ORE = 74;
	const REDSTONE_TORCH_OFF = 75;
	const REDSTONE_TORCH_ON = 76;
	const STONE_BUTTON = 77;
	const SNOW = 78;
	const ICE = 79;
	const SNOW_BLOCK = 80;
	const CACTUS = 81;
	const CLAY = 82;
	const REED = 83;
	const JUKEBOX = 84;
	const FENCE = 85;
	const PUMPKIN = 86;
	const NETHERSTONE = 87;
	const NETHERRACK = 87;
	const SLOW_SAND = 88;
	const LIGHTSTONE = 89;
	const PORTAL = 90;
	const JACKOLANTERN = 91;
	const CAKE_BLOCK = 92;
	const REDSTONE_REPEATER_OFF = 93;
	const REDSTONE_REPEATER_ON = 94;
	const LOCKED_CHEST = 95;
	const TRAP_DOOR = 96;
	const SILVERFISH_BLOCK = 97;
	const STONE_BRICK = 98;
	const BROWN_MUSHROOM_CAP = 99;
	const RED_MUSHROOM_CAP = 100;
	const IRON_BARS = 101;
	const GLASS_PANE = 102;
	const MELON_BLOCK = 103;
	const PUMPKIN_STEM = 104;
	const MELON_STEM = 105;
	const VINE = 106;
	const FENCE_GATE = 107;
	const BRICK_STAIRS = 108;
	const STONE_BRICK_STAIRS = 109;

	// Item
	const IRON_SHOVEL = 256;
	const IRON_PICK = 257;
	const IRON_AXE = 258;
	const FLINT_AND_TINDER = 259;
	const RED_APPLE = 260;
	const BOW = 261;
	const ARROW = 262;
	const COAL = 263;
	const DIAMOND = 264;
	const IRON_BAR = 265;
	const GOLD_BAR = 266;
	const IRON_SWORD = 267;
	const WOOD_SWORD = 268;
	const WOOD_SHOVEL = 269;
	const WOOD_PICKAXE = 270;
	const WOOD_AXE = 271;
	const STONE_SWORD = 272;
	const STONE_SHOVEL = 273;
	const STONE_PICKAXE = 274;
	const STONE_AXE = 275;
	const DIAMOND_SWORD = 276;
	const DIAMOND_SHOVEL = 277;
	const DIAMOND_PICKAXE = 278;
	const DIAMOND_AXE = 279;
	const STICK = 280;
	const BOWL = 281;
	const MUSHROOM_SOUP = 282;
	const GOLD_SWORD = 283;
	const GOLD_SHOVEL = 284;
	const GOLD_PICKAXE = 285;
	const GOLD_AXE = 286;
	const STRING = 287;
	const FEATHER = 288;
	const SULPHUR = 289;
	const WOOD_HOE = 290;
	const STONE_HOE = 291;
	const IRON_HOE = 292;
	const DIAMOND_HOE = 293;
	const GOLD_HOE = 294;
	const SEEDS = 295;
	const WHEAT = 296;
	const BREAD = 297;
	const LEATHER_HELMET = 298;
	const LEATHER_CHEST = 299;
	const LEATHER_PANTS = 300;
	const LEATHER_BOOTS = 301;
	const CHAINMAIL_HELMET = 302;
	const CHAINMAIL_CHEST = 303;
	const CHAINMAIL_PANTS = 304;
	const CHAINMAIL_BOOTS = 305;
	const IRON_HELMET = 306;
	const IRON_CHEST = 307;
	const IRON_PANTS = 308;
	const IRON_BOOTS = 309;
	const DIAMOND_HELMET = 310;
	const DIAMOND_CHEST = 311;
	const DIAMOND_PANTS = 312;
	const DIAMOND_BOOTS = 313;
	const GOLD_HELMET = 314;
	const GOLD_CHEST = 315;
	const GOLD_PANTS = 316;
	const GOLD_BOOTS = 317;
	const FLINT = 318;
	const RAW_PORKCHOP = 319;
	const COOKED_PORKCHOP = 320;
	const PAINTING = 321;
	const GOLD_APPLE = 322;
	const SIGN = 323;
	const WOODEN_DOOR_ITEM = 324;
	const BUCKET = 325;
	const WATER_BUCKET = 326;
	const LAVA_BUCKET = 327;
	const MINECART = 328;
	const SADDLE = 329;
	const IRON_DOOR_ITEM = 330;
	const REDSTONE_DUST = 331;
	const SNOWBALL = 332;
	const WOOD_BOAT = 333;
	const LEATHER = 334;
	const MILK_BUCKET = 335;
	const BRICK_BAR = 336;
	const CLAY_BALL = 337;
	const SUGAR_CANE_ITEM = 338;
	const PAPER = 339;
	const BOOK = 340;
	const SLIME_BALL = 341;
	const STORAGE_MINECART = 342;
	const POWERED_MINECART = 343;
	const EGG = 344;
	const COMPASS = 345;
	const FISHING_ROD = 346;
	const WATCH = 347;
	const LIGHTSTONE_DUST = 348;
	const RAW_FISH = 349;
	const COOKED_FISH = 350;
	const INK_SACK = 351;
	const BONE = 352;
	const SUGAR = 353;
	const CAKE_ITEM = 354;
	const BED_ITEM = 355;
	const REDSTONE_REPEATER = 356;
	const COOKIE = 357;
	const MAP = 358;
	const SHEARS = 359;
	const MELON = 360;
	const PUMPKIN_SEEDS = 361;
	const MELON_SEEDS = 362;
	const RAW_BEEF = 363;
	const COOKED_BEEF = 364;
	const RAW_CHICKEN = 365;
	const COOKED_CHICKEN = 366;
	const ROTTEN_FLESH = 367;
	const ENDER_PEARL = 368;
	const GOLD_RECORD = 2256;
	const GREEN_RECORD = 2257;

	/**
	 * Get block name by ID
	 *
	 * @param id						Block ID
	 */
	public static function block_name($id) {
		$block_name = array(
			22		=> 'lapiz block',
			41		=> 'gold block',
			42		=> 'iron block',
			57		=> 'diamond block',
			88		=> 'soulsand',
			89		=> 'glowstone',
			20		=> 'glass',
			self::BRICK	=> 'brick',
			self::CLOTH	=> 'wool',
		);
		if(strpos($id, ':') !== FALSE) {
			$id = array_shift(explode(':', $id));
		}
		if(isset($block_name[$id])) {
			return $block_name[$id];
		}
		return "unknown (itemid: $id)";
	}

	/**
	 * Given a world dir, this will return an array of players
	 *
	 * @param world_dir					World directory
	 * @return array					All players ever been in that world
	 */
	public static function minecraft_get_players($world_dir) {
		$player_dir = $world_dir . '/players';
		if(!isset($player_dir) || !is_dir($player_dir)) {
			return array();
		}
		$players = array();
		foreach(glob($player_dir . '/*.dat') as $player) {
			$players[] = substr(array_pop(explode('/', $player)), 0, -4);
		}
		return $players;
	}

	public function get_item_name($item_id) {
		if(empty($this->cache_items)) {
			$reflect = new ReflectionObject($this);
			foreach($reflect->getConstants() as $name => $id) {
				$this->cache_items[$id] = $name;
			}
		}
		return isset($this->cache_items[$item_id])
			? strtolower($this->cache_items[$item_id])
			: 'Unknown';
	}
}
