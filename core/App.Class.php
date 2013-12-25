<?php
class App extends Base {
	// 控制器名及方法名
	static private $ctrl_name;
	static private $action_name;
	// URL中的参数列表
	static public $params = array();
	
	// 解析URL地址
	static private function parseURLS() {
		if (file_exists(URLS)) {
			$pathinfo = filter_input(INPUT_SERVER, 'PATH_INFO');
			// 指定cache的存储空间为URLS
			LeapCache::setPrefix(leapJoin('URLS_', md5(APP_ABS_PATH)));
			// 尝试从cache中读取路径配置
			$cache_data = LeapCache::get(md5($pathinfo));
			if ($cache_data) {
				// 如果缓存了URL转发路径
				list(self::$ctrl_name, self::$action_name, self::$params) = json_decode($cache_data, true);
			} else {
				// 没缓存URL转发路径，加载路径配置文件
				$urls = require_once URLS;
				// 开始匹配URL地址
				foreach ((array)$urls as $pattern => $handler) {
					preg_match($pattern, $pathinfo, self::$params);
					// 匹配到合适的路径配置
					if (!empty(self::$params)) {
						list(self::$ctrl_name, self::$action_name) = explode('.', $handler);
						unset(self::$params[0]);
						// 缓存URL路径转发
						$cache_data = array(self::$ctrl_name, self::$action_name, self::$params);
						LeapCache::set(md5($pathinfo), json_encode($cache_data));
						break;
					}
				}
			}
		} else {
			// 如果没有找到URL配置文件
			throw new Exception('Could not find router file.', 824200003);
		}

		if (!isset(self::$ctrl_name) || !isset(self::$action_name)) {
			throw new Exception('Could not find router rule.', 824200010);
		}

		// 引入控制器类文件
		$ctrl_file = leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, ACTION_DIR, DS, self::$ctrl_name, '.ctrl.php');
		require_once $ctrl_file;
	}
	
	// 初始化使用框架的应用常量
	static private function initialize() {
		// 应用的访问URI
		define('ENTRY_URI', filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
		// 应用的入口文件名
		define('ENTRY_FILE', pathinfo(ENTRY_URI)['basename']);
		// 应用的访问URI目录
		define('PATH', dirname(ENTRY_URI));
	}

	// 应用开始，框架入口
	static public function run() {
		// 初始化应用常量
		self::initialize();
		// 解析URL地址
		self::parseURLS();

		// 实例化控制器类
		$app = new self::$ctrl_name;
		if (method_exists($app, self::$action_name)) {
			// 动态调用控制器类中的方法
			$method = self::$action_name;
			call_user_func_array(array($app, $method), self::$params);
		} else {
			throw new Exception('Unsigned action.', 824200005);
		}
	}
}
