<?php
leapCheckEnv();
/**
 * 路由分发器
 * 
 * @author hliang
 * @package leaphp
 * @subpackage core
 * @since 1.0.0
 *
 */
class Dispatch extends Base {
	static private $routers = array();
	static private $params = array();
	
	/**
	 * 开始进行路由分发
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 */
	static public function route() {
		$logger = LeapLogger::getLogger('lpf_mainloop::' . __METHOD__);
		$logger->trace('开始进行dispatch规则匹配。');
		
		self::loadDispRouter();
		
		// 逐一读取路由规则并分析
		foreach (self::$routers as $router) {
			list($methods, $path, $callback) = $router;
			preg_match($path, _server('PATH_INFO'), self::$params);
			if (self::$params) {
				$logger->trace('成功匹配到合适的dispatch规则 -> ' . $path);
				// 读取到匹配的第一条记录，跳出
				break;
			}
		}
		
		// self::$params的第一项为匹配出的pathinfo信息
		$pathinfo = array_shift(self::$params);
		
		// 匹配合法的路由信息
		$route_info = array(
			'methods' => $methods,
			'pathinfo' => $pathinfo,
			'callback' => $callback,
			'params' => self::$params,
		);
		
		$logger->trace('成功进行dispatch规则匹配 -> ' . $callback);
		
		return (object)$route_info;
	}
	
	/**
	 * append方法的别名
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 */
	static public function add($method, $path, $callback) {
		self::append($method, $path, $callback);
	}
	
	/**
	 * 增加一条路由分发规则
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $method
	 * @param string $path
	 * @param string $callback
	 */
	static public function append($method, $path, $callback) {
		$logger = LeapLogger::getLogger('lpf_mainloop::' . __METHOD__);
		$logger->trace(leapJoin('请求方法 -> ', json_encode($method), '; 路径规则 -> ', $path, '; 回调方法 -> ', $callback));
		
		array_push(self::$routers, array(
			(array)$method, $path, $callback,
		));
	}
	
	/**
	 * 加载dispatch路由规则文件
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 */
	static private function loadDispRouter() {
		if (file_exists(DISPATCH)) {
			require_once DISPATCH;
		}
	}
}
