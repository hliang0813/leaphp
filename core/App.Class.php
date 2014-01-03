<?php
/**
 * 应用程序入口类，应用程序的总入口
 * 
 * @author hliang
 * @package leaphp
 * @subpackage core
 * @since 1.0.0
 *
 */
class App extends Base {
	// controller及action名称
	static private $controller;
	static private $action;
	
	// dispatch在缓存中的生存周期
	static private $dispatch_cache_ttl = 20;
	
	/**
	 * 初始化框架模板页会用到的常量
	 * 
	 * ENTRY_URI 入口文件的绝对uri，不带域名、不带pathinfo、不带参数
	 * ENTRY_FILE 入口文件名
	 * PATH 入口文件的uri路径，不带文件名
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 */
	static private function initialize() {
		$logger = LeapLogger::getLogger(__METHOD__);
		// 应用的访问URI
		define('ENTRY_URI', _server('SCRIPT_NAME'));
		$logger->trace(leapJoin('ENTRY_URI:', ENTRY_URI));
		
		// 应用的入口文件名
		define('ENTRY_FILE', pathinfo(ENTRY_URI)['basename']);
		$logger->trace(leapJoin('ENTRY_FILE:', ENTRY_FILE));
		
		// 应用的访问URI目录
		define('PATH', dirname(ENTRY_URI));
		$logger->trace(leapJoin('PATH:', PATH));
	}
	
	static private function buildinDispatch() {
		// 固定的内置dispatch
		Dispatch::append('GET', '/^\/buildin\/resource.pack$/', 'ResourcePack::webInterface');
	}

	/**
	 * 应用开始，框架主入口
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @throws LeapException
	 */
	static public function run() {
		$logger = LeapLogger::getLogger(__METHOD__);
		
		// 初始化应用常量
		self::initialize();
		
		self::buildinDispatch();
		
		// 初始化并设置dispatcher的缓存前缀及key
		LeapCache::setPrefix('LEAPDISPATCH');
		$dispatch_cache_key = leapJoin(APP_ABS_PATH, '_', _server('PATH_INFO'));
		$logger->trace(leapJoin('dispatch_cache_key:', $dispatch_cache_key));
		
		// 尝试从缓存中读取dispatch信息
		$disp_res = LeapCache::get($dispatch_cache_key);
		
		if (!$disp_res) {
			// 如果缓存读取失败，从dispatch文件中获取，并写入缓存
			$disp_res = Dispatch::route();
			$logger->trace(leapJoin('dispatch_from_file:', var_export($disp_res, true)));
			
			$cache_result = LeapCache::set($dispatch_cache_key, json_encode($disp_res), self::$dispatch_cache_ttl);
			$logger->trace(leapJoin('set_dispatch_cache:', var_export($cache_result, true)));
		} else {
			// 读取成功，将缓存中数据转换成可以使用的object
			$disp_res = json_decode($disp_res);
			$logger->trace(leapJoin('dispatch_from_cache:', var_export($disp_res, true)));
		}
		
		// 如果路由的pathinfo不存在，即访问一个不合法的地址
		if ($disp_res->pathinfo === NULL) {
			$logger->trace(leapJoin('pathinfo_not_found:', _server('PATH_INFO')));
			LeapFunction('sendheader', 404);
		}
		
		// 检查请求方式是否正确
		if (!in_array(_server('REQUEST_METHOD'), $disp_res->methods)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Request method does not allowed.'));
		}
		
		// 获取controller和action的名称
		list(self::$controller, self::$action) = explode('::', $disp_res->callback);
		$logger->trace(leapJoin('controller:', self::$controller, ';action:', self::$action));
				
		// 如果controller类不存在
		if (!class_exists(self::$controller)) {
			// 检查controller文件是否存在，并引入
			$controller_file = leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, CONTROLLER_DIR, DS, self::$controller, '.ctrl.php');
			$logger->trace(leapJoin('controller_file:', $controller_file));
			if (file_exists($controller_file)) {
				require_once $controller_file;
			} else {
				throw new LeapException(LeapException::leapMsg(__METHOD__, 'Unsigned controller.'));
			}
			
			// 实例化controller
			$app = new self::$controller;
		} else {
			$app = self::$controller;
		}
					
		// 检查controller中是否有action方法，并调用
		if (method_exists($app, self::$action)) {
			call_user_func_array(array($app, self::$action), $disp_res->params);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Unsigned action.'));
		}
		
		
	}
	
	static public function getController() {
		return self::$controller;
	}
	
	static public function getAction() {
		return self::$action;
	}
}
