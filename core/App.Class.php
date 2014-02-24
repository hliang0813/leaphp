<?php
use Assetic\Exception\Exception;
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
		// 应用的访问URI
		define('ENTRY_URI', _server('SCRIPT_NAME'));
		// 应用的入口文件名
		$pathinfo = pathinfo(ENTRY_URI);
		define('ENTRY_FILE', $pathinfo['basename']);
		// 应用的访问URI目录
		define('PATH', dirname(ENTRY_URI));
	}
	
	static private function buildinDispatch() {
		// 打包javascript资源
		Dispatch::append('GET', '/buildin/resource.js', 'ResourcePack::webInterface');
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
		// 初始化应用常量
		self::initialize();
		
		// 初始化并设置dispatcher的缓存前缀及key
		LeapCache::setPrefix('LEAPDISPATCH');
		$dispatch_cache_key = leapJoin(APP_ABS_PATH, '_', _server('PATH_INFO'));
		
		// 尝试从缓存中读取dispatch信息
		$disp_res = LeapCache::get($dispatch_cache_key);
		if (!$disp_res) {
			// 加载框架内部dispatch
			self::buildinDispatch();
			// 如果缓存读取失败，从dispatch文件中获取，并写入缓存
			$disp_res = Dispatch::route();
			if ($disp_res->error !== NULL) {
				throw new LeapException('解析Dispatch信息失败：' . $disp_res->body, -99999);
			}
			$cache_result = LeapCache::set($dispatch_cache_key, json_encode($disp_res->body), self::$dispatch_cache_ttl);
			$disp_res = $disp_res->body;
		} else {
			// 读取成功，将缓存中数据转换成可以使用的object
			$disp_res = JSON::decode($disp_res);
			if ($disp_res->error !== NULL) {
				throw new LeapException('LPF', '读取缓存中的Dispatch信息失败：' . $disp_res->body, -99999);
			}
			$disp_res = $disp_res->body;
		}
		
		// 如果路由的pathinfo不存在，即访问一个不合法的地址
		if ($disp_res->pathinfo === NULL) {
			throw new LeapException('LPF', '请求的路径 [' . _server('PATH_INFO') . '] 不存在', -99999);
			LeapFunction('sendheader', 404);
		}
		
		// 检查请求方式是否正确
		if (!in_array(_server('REQUEST_METHOD'), $disp_res->methods)) {
			throw new LeapException('LPF', '不允许向路径 [' . _server('PATH_INFO') . '] 发起 ' . _server('REQUEST_METHOD') . ' 方式的请求', -99999);
		}
		
		// 匹配通配方法::*
		if (strpos($disp_res->callback, '::*') !== false) {
			$_common_method = array_shift($disp_res->params);
			$disp_res->callback = str_replace('::*', '::' . $_common_method, $disp_res->callback);
		}
		
		// 获取controller和action的名称
		list(self::$controller, self::$action) = explode('::', $disp_res->callback);
		if (trim(self::$controller) == '') {
			throw new LeapException('LPF', '未找到对应的Controller [' . self::$controller . ']', -99999);
		}
		if (trim(self::$action) == '') {
			throw new LeapException('LPF', '未找到对应的Action [' . self::$action . ']', -99999);
		}
		
		// 如果controller类不存在
		if (!class_exists(self::$controller)) {
			// 检查controller文件是否存在，并引入
			$controller_file = leapJoin(CONTROLLER_DIR, DS, self::$controller, '.Ctrl.php');
			if (file_exists($controller_file)) {
				require_once $controller_file;
			} else {
				throw new LeapException('LPF', '未找到Controller对应的文件 [' . $controller_file . ']', -99999);
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
			throw new LeapException('LPF', 'Controller中未注册的方法 [' . self::$action . ']', -99999);
		}
	}
	
	static public function getController() {
		return Base::response(self::$controller);
	}
	
	static public function getAction() {
		return Base::response(self::$action);
	}
}
