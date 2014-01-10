<?php
leapCheckEnv();
require_once __DIR__ . '/Memcached.Class.php';

/**
 * 從Memcached繼承封閉的Memcache協議緩存操作類
 * 用於操作Memcache以及Kestrel
 * 
 * @author hliang
 * @package leaphp 
 * @subpackage sysplugins
 * @since 1.0.0
 *
 */
class MemcacheClient extends Memcached {
	/**
	 * 構造函數，加載配置文件
	 * 
	 * @author hliang
	 * @since 1.0.0 
	 * 
	 * @param string $config
	 */
	public function __construct($config = 'memcache') {
		$default_config = array(
			'servers' => array('127.0.0.1:11211'),
			'debug' => false,
			'compress_threshold' => 10240,
			'persistant' => false,
		);
		$user_config = LeapConfigure::get($config);
		$config = LeapFunction('array_merge', $default_config, $user_config ? $user_config : array());
		
		parent::Memcached($config);
	}
}
