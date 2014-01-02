<?php
require_once leapJoin(__DIR__, DS, 'log4php', DS, 'Logger.php');

class LeapLogger extends Logger {
	static private $is_load_config = false;
	
	public static function configure($configuration = null, $configurator = null) {
		if (!self::$is_load_config) {
			parent::configure($configuration, $configurator);
			self::$is_load_config = true;
		}
	}

	public static function getLogger($name) {
		self::configure(LeapConfigure::get('logger'), 'LoggerConfiguratorPhp');
		return parent::getLogger($name);
	}
}