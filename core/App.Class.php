<?php
leapCheckEnv();
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
		$logger = LeapLogger::getLogger('lpf_mainloop::' . __METHOD__);
		$logger->trace('开始进行应用常量初始化。');
		// 应用的访问URI
		define('ENTRY_URI', _server('SCRIPT_NAME'));
		$logger->trace(leapJoin('常量ENTRY_URI -> ', ENTRY_URI));
		
		// 应用的入口文件名
		define('ENTRY_FILE', pathinfo(ENTRY_URI)['basename']);
		$logger->trace(leapJoin('常量ENTRY_FILE -> ', ENTRY_FILE));
		
		// 应用的访问URI目录
		define('PATH', dirname(ENTRY_URI));
		$logger->trace(leapJoin('常量PATH -> ', PATH));
		$logger->trace('成功进行应用常量初始化。');
	}
	
	static private function buildinDispatch() {
		$logger = LeapLogger::getLogger('lpf_mainloop::' . __METHOD__);
		$logger->trace('开始设置框架内部dispatcher。');
		// 打包javascript资源
		Dispatch::append('GET', '/buildin/resource.js', 'ResourcePack::webInterface');
		$logger->trace('成功设置框架内部dispatcher。');
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
		$logger = LeapLogger::getLogger('lpf_mainloop::' . __METHOD__);
		$logger->trace('成功进入App::run()方法，并执行主循环。');
		
		// 初始化应用常量
		self::initialize();
		
		// 初始化并设置dispatcher的缓存前缀及key
		LeapCache::setPrefix('LEAPDISPATCH');
		$dispatch_cache_key = leapJoin(APP_ABS_PATH, '_', _server('PATH_INFO'));
		$logger->trace('返回App::run()并生成dispatcher的缓存key -> ' . $dispatch_cache_key);
		
		// 尝试从缓存中读取dispatch信息
		$disp_res = LeapCache::get($dispatch_cache_key);
		
		if (!$disp_res) {
			// 加載框架內部dispatch
			self::buildinDispatch();

			$logger->trace('缓存中不存在当前请求的pathinfo -> ' . _server('PATH_INFO'));
			// 如果缓存读取失败，从dispatch文件中获取，并写入缓存
			$disp_res = Dispatch::route();
			$logger->trace('从dispatch规则中加载匹配当前pathinfo的匹配信息 -> ' . var_export($disp_res, true));
			
			$cache_result = LeapCache::set($dispatch_cache_key, json_encode($disp_res), self::$dispatch_cache_ttl);
			$logger->trace('成功将dispatch匹配规则写入缓存。');
		} else {
			$logger->trace('缓存中存在当前请求的pathinfo。');
			// 读取成功，将缓存中数据转换成可以使用的object
			$disp_res = json_decode($disp_res);
			$logger->trace('从缓存中加载匹配当前pathinfo的规则 -> ' . var_export($disp_res, true));
		}
		
		// 如果路由的pathinfo不存在，即访问一个不合法的地址
		if ($disp_res->pathinfo === NULL) {
			$logger->trace('请求的pathinfo不存在，返回404响应码。');
			$logger->trace('应用中断执行。 ');
			LeapFunction('sendheader', 404);
		}
		
		// 检查请求方式是否正确
		if (!in_array(_server('REQUEST_METHOD'), $disp_res->methods)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Request method does not allowed.'));
		}
		
		// 获取controller和action的名称
		list(self::$controller, self::$action) = explode('::', $disp_res->callback);
		$logger->trace('成功解析到controller信息。控制器 -> ' . self::$controller . '; 方法 -> ' . self::$action);
				
		// 如果controller类不存在
		if (!class_exists(self::$controller)) {
			// 检查controller文件是否存在，并引入
			$controller_file = leapJoin(CONTROLLER_DIR, DS, self::$controller, '.ctrl.php');
			if (file_exists($controller_file)) {
				require_once $controller_file;
				$logger->trace('成功加载普通的controller类文件 -> ' . $controller_file);
			} else {
				throw new LeapException(LeapException::leapMsg(__METHOD__, 'Unsigned controller.'));
			}
			
			// 实例化controller
			$app = new self::$controller;
		} else {
			$app = self::$controller;
			$logger->trace('成功加载内置的controller类 -> ' . self::$controller);
		}
					
		// 检查controller中是否有action方法，并调用
		if (method_exists($app, self::$action)) {
			call_user_func_array(array($app, self::$action), $disp_res->params);
			$logger->trace('成功调用controller中对应的方法 -> ' . self::$action);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Unsigned action.'));
		}
		
		$logger->trace('成功执行完毕App::run()主循环方法。');
		
		
	}
	
	static public function getController() {
		return Base::response(self::$controller);
	}
	
	static public function getAction() {
		return Base::response(self::$action);
	}
}
