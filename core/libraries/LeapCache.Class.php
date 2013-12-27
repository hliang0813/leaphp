<?php
/**
 * 定义了一个缓存对象会使用到的接口
 * 对缓存的定义，要求必须至少有setPrefix、set、get、delete四个方法
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
interface CacheInterface {
	
	/**
	 * 设置缓存中key的前缀
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $prefix
	 */
	static public function setPrefix($prefix = '');
	
	/**
	 * 向缓存中写内容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $key
	 * @param string $value
	 * @param number $ttl
	 */
	static public function set($key = '', $value = '', $ttl = 0);
	
	/**
	 * 读取缓存中key的内容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $key
	 */
	static public function get($key = '');
	
	/**
	 * 删除缓存中key的内容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $key
	 */
	static public function delete($key = '');
}


/**
 * 使用APC作为框架的缓存
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
class CacheAPC implements CacheInterface {
	static private $prefix = '';
	
	/**
	 * 实现了接口中的setPrefix方法
	 * 
	 * @author hliang
	 * @since 
	 * 
	 * @param string $prefix
	 */
	static public function setPrefix($prefix = '') {
		self::$prefix = $prefix;
	}
	
	/**
	 * 实现了接口中的set方法
	 * 
	 * @author hliang
	 * @since 
	 * 
	 * @param string $key
	 * @param string $value
	 * @param number $ttl
	 * @throws LeapException
	 * @return boolean
	 */
	static public function set($key = '', $value = '', $ttl = 0) {
		if (function_exists('apc_store')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_store($_store_key, $value, $ttl);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'APC module not found.'));
		}
	}
	
	/**
	 * 实现了接口中的get方法
	 * 
	 * @author hliang
	 * @since 
	 * 
	 * @param string $key
	 * @throws LeapException
	 * @return mixed
	 */
	static public function get($key = '') {
		if (function_exists('apc_fetch')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_fetch($_store_key);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'APC module not found.'));
		}
	}
	
	/**
	 * 实现了接口中的delete方法
	 * 
	 * @author hliang
	 * @since 
	 * 
	 * @param string $key
	 * @throws LeapException
	 * @return mixed
	 */
	static public function delete($key = '') {
		if (function_exists('apc_delete')) {
			$_store_key = leapJoin(self::$prefix, '_', $key);
			return apc_delete($_store_key);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'APC module not found.'));
		}
	}
}


if (function_exists('apc_add')) {
	class LeapCache extends CacheAPC {}
} else {
	throw new LeapException(LeapException::leapMsg(__METHOD__, 'APC module not found.'));
}
