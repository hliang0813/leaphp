<?php
class App extends Base {
	// 应用的绝对路径
	static private $app_abs_path;
	// 控制器文件绝对地址
	static private $ctrl_abs_path;
	// 控制器名及方法名
	static private $name_ctrl;
	static private $name_action;
	// URL中的参数列表
	static public $params = array();
	
	// 解析URL地址
	static private function handleFunc() {
		if (!file_exists(URLS)) {
			// 如果没有找到URL配置文件
			throw new Exception('Could not find router file.', 824200003);
		} else {
			$pathinfo = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO'];
			if ($cache_data = LeapCache::get('urls', md5($pathinfo))) {
				// 如果缓存了url转发路径
				list(self::$name_ctrl, self::$name_action, self::$params) = json_decode($cache_data, true);
			} else {
				// 没缓存url转发路径
				$urls = require_once URLS;
				// 开始匹配URL地址
				foreach ((array)$urls as $pattern => $handler) {
					preg_match($pattern, $pathinfo, self::$params);
					if (!empty(self::$params)) {
						list(self::$name_ctrl, self::$name_action) = explode('.', $handler);
						unset(self::$params[0]);
						// 缓存url路径转发
						$cache_data = array(self::$name_ctrl, self::$name_action, self::$params);
						LeapCache::set('urls', md5($pathinfo), json_encode($cache_data));
						break;
					}
				}
			}
		}

		if (!isset(self::$name_ctrl) || !isset(self::$name_action)) {
			throw new Exception('Could not find router rule.', 824200010);
		}

		// 引入控制器类文件
		$ctrl_file = self::$ctrl_abs_path . DS . self::$name_ctrl . '.ctrl.php';
		require_once $ctrl_file;
	}
	
	// 初始化框架常量
	static private function initializeConst($app_path) {
		$script_name = pathinfo($_SERVER['SCRIPT_NAME']);
		// 应用的访问URI
		define('ENTRY_URI', $_SERVER['SCRIPT_NAME']);
		// 应用的入口文件名
		define('ENTRY_FILE', $script_name['basename']);
		// 应用的访问URI目录
		define('PATH', dirname(ENTRY_URI));

		// 初始化应用的绝对路径
		self::$app_abs_path = $app_path ? $app_path : __DIR__ . DS . '..';
		define('APP_ABS_PATH', self::$app_abs_path);
		// 控制器目录绝对地址
		self::$ctrl_abs_path = self::$app_abs_path . DS . APP_NAME . DS . ACTION_DIR;
	}

	// 初始化配置文件
	static private function initializeConfig() {
		$config_file = CONFIG_DIR . DS . 'configure.ini';
		if (file_exists($config_file)) {
			$_SERVER['LEAPHP_CONFIGURE'] = parse_ini_file($config_file, true);
		} else {
			throw new Exception("Error on loading database configuration file.", 824209001);
		}
	}

	// 应用开始
	static public function run($app_path = NULL) {
		// 初始化框架常量
		self::initializeConst($app_path);
		// 初始化配置文件
		self::initializeConfig();
		// 解析URL地址
		self::handleFunc();

		// 实例化控制器类
		$app = new self::$name_ctrl;
		if (method_exists($app, self::$name_action)) {
			// 动态调用控制器类中的方法
			$method = self::$name_action;
			call_user_func_array(array($app, $method), self::$params);
		} else {
			throw new Exception('Unsigned action.', 824200005);
		}
	}
}
