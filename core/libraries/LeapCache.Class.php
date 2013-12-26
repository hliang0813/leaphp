<?php
///////////////////////////////////////////////////////////////
// 定义一个CACHE的接口
///////////////////////////////////////////////////////////////
interface CacheInterface {
	// 设置缓存key的前缀
	static public function setPrefix($prefix);
	// 设置缓存内容
	static public function set($key, $value, $ttl);
	// 读取缓存内容
	static public function get($key);
	// 删除缓存内容
	static public function delete($key);
}


///////////////////////////////////////////////////////////////
// 使用APC缓存
///////////////////////////////////////////////////////////////
class CacheAPC implements CacheInterface {
	static private $prefix = '';
	// 前缀
	static public function setPrefix($prefix) {
		self::$prefix = $prefix;
	}
	// 写
	static public function set($key, $value, $ttl = 0) {
		if (function_exists('apc_store')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_store($_store_key, $value, $ttl);
		} else {
			throw new Exception('APC module not found.');
		}
	}
	
	// 读
	static public function get($key) {
		if (function_exists('apc_fetch')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_fetch($_store_key);
		} else {
			throw new Exception('APC module not found.');
		}
	}
	
	// 删
	static public function delete($key) {
		if (function_exists('apc_delete')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_delete($_store_key);
		} else {
			throw new Exception('APC module not found.');
		}
	}
}


if (function_exists('apc_add')) {
	class LeapCache extends CacheAPC {}
} else {
	throw new Exception('APC module not found.');
}
