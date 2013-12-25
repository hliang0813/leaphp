<?php
class LeapConfigure {
	static private $configure;
	
	public static function set($config) {
		self::$configure = $config;
	}
	
	public static function get($key) {
		if (key_exists($key, self::$configure)) {
			return self::$configure[$key];
		} else {
			return false;
		}
	}
	
	public static function getAll() {
		return self::$configure;
	}
}