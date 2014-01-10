<?php
leapCheckEnv();
/**
 * 从框架指定的配置文件中读取配置项
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
class LeapConfigure {
	static private $configure;
	
	/**
	 * 读取全部配置内容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param array $config
	 */
	public static function load($config) {
		self::$configure = $config;
	}
	
	
	/**
	 * 读取配置项中的某项
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $key
	 * @return array|boolean
	 */
	public static function get($key = NULL) {
		if (!$key) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Configure key cannot be empty.'));
		}
		if (key_exists($key, self::$configure)) {
			return self::$configure[$key];
		} else {
			return false;
		}
	}
	
	/**
	 * 读取全部配置项内容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public static function getAll() {
		return self::$configure;
	}
}
