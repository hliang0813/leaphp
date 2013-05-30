<?php
class LeapCache extends Base {
	static private $cache_dir;
	static private $cache_file;

	static private function init($group) {
		self::$cache_dir = APP_ABS_PATH . DS . APP_NAME . DS . CACHE_DIR;
		// 缓存目录不存在，创建缓存目录
		if (!file_exists(self::$cache_dir)) {
			mkdir(self::$cache_dir);
		}
		self::$cache_file = self::$cache_dir . DS . $group . '.cache';
		// 缓存文件不存在，返回false
		if (!file_exists(self::$cache_file)) {
			return array();
		}
		// 读取缓存文件并返回
		$cache_list = include self::$cache_file;
		return $cache_list;
	}

	// 向缓存中写内容
	static public function set($group, $key, $value, $expire = 0) {
		$cache = self::init($group);
		// 如果没有这个缓存，将缓存内容设置为空
		if (!$cache) {
			$cache = array();
		}
		// 将新key增加到缓存内
		$cache[$key] = array(
			// 缓存内容
			'data' => $value, 
			// 超时时间
			'expire' => $expire == 0 ? 0 : time() + $expire
		);
		// 生成缓存内容
		$cache_string = '<?php return ' . var_export($cache, true) . ';';
		if (file_put_contents(self::$cache_file, $cache_string)) {
			// 缓存文件保存成功，返回true
			return true;
		} else {
			return false;
		}
	}

	// 从缓存中读取
	static public function get($group, $key) {
		$cache = self::init($group);
		if (array_key_exists($key, $cache)) {
			// key值存在
			return $cache[$key]['data'];
		} else {
			return false;
		}
	}

	static public function del($group, $key) {

	}

	static public function flush($group = '') {

	}
}