<?php
require_once __DIR__ . '/Memcached.Class.php';

/**
 * 类名：Cache
 * 描述：封闭的缓存操作类
 * @author hliang
 * @copyright Copyright (c) 2011- neusoft
 * @version 0.1
 */
class MemcacheClient extends Memcached {
	/**
	 * 函数名：__construct
	 * 描述：构造函数
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
		$config = array_merge($default_config, $user_config ? $user_config : array());
		parent::Memcached($config);
	}
}