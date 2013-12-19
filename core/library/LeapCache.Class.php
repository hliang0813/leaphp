<?php

class LeapCache extends Base {
	static private $cache_file = '';
	static private $cache_content_prev = '<?php return ';

	static private function loadCacheFile() {
		if (file_exists(self::$cache_file)) {
			$cache = include self::$cache_file;
			return $cache;
		}
	}

	static private function makeCacheContent($content) {
		return self::$cache_content_prev . $content . ';';
	}


	static public function setPrefix($c_file) {
		if (function_exists('apc_add')) {
			self::$cache_file = $c_file . '_';
		} else {
			self::$cache_file = APP_ABS_PATH . DS . APP_NAME . DS . CACHE_DIR . DS . $c_file . '.cache';
			if (!file_exists(dirname(self::$cache_file))) {
				LeapFunction('mkdirs', dirname(self::$cache_file));
			}
			if (!file_exists(self::$cache_file)) {
				file_put_contents(self::$cache_file, self::makeCacheContent('array()'));
			}
		}
	}

	static public function set($key, $value, $expire = 0) {
		if (function_exists('apc_add')) {
			return apc_add(self::$cache_file . $key, $value);
		} else {
			$exist_cache = self::loadCacheFile();
			$exist_cache[$key] = $value;

			file_put_contents(self::$cache_file, self::makeCacheContent(var_export($exist_cache, true)));
		}
	}

	static public function get($key) {
		if (function_exists('apc_fetch')) {
			return apc_fetch(self::$cache_file . $key);
		} else {
			$exist_cache = self::loadCacheFile();
			if (array_key_exists($key, (array)$exist_cache)) {
				return $exist_cache[$key];
			} else {
				return null;
			}
		}
	}
}

