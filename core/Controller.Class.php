<?php
leapCheckEnv();
/**
 * 控制器基类，从Base继承
 * 
 * @author hliang
 * @package leaphp
 * @subpackage core
 * @since 1.0.0
 *
 */
class Controller extends Base {
	static private $template;

	/**
	 * 动态调用controller基类加载的扩展功能包
	 * 使用时，以定义的三个字母的前缀加下划线加原功能包方法名调用原方法
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $method
	 * @param unknown $params
	 * @throws LeapException
	 */
	public function __call($method, $params) {
		self::_callMethod($method, $params);
	}

	/**
	 * 静态调用controller基类加载的扩展功能包
	 * 使用时，以定义的三个字母的前缀加下划线加原功能包方法名调用原方法
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $method
	 * @param unknown $params
	 * @throws LeapException
	 */
	static public function __callStatic($method, $params) {
		self::_callMethod($method, $params);
	}

	/**
	 * 调用controller基类加载的扩展功能包
	 * 使用时，以定义的三个字母的前缀加下划线加原功能包方法名调用原方法
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $method
	 * @param unknown $params
	 * @throws LeapException
	 */
	private function _callMethod($method, $params) {
		$_switcher = explode('_', $method, 2);
		switch ($_switcher[0]) {
			case 'tpl':
				// 根据前缀来判断调用何种方法
				$tpl_method = substr($method, 4);
				if (!is_object(self::$template)) {
					// 引入模板类
					require_once leapJoin(__DIR__, DS, 'libraries', DS, 'template', DS, 'LeapTemplate.Class.php');
					// 初始化模板对象
					self::$template = new LeapTemplate;
				}
				if (method_exists(self::$template, $tpl_method)) {
					// 动态调用方法
					call_user_func_array(array(self::$template, $tpl_method), $params);
				} else {
					throw new LeapException('LPF', '没有找到模板类方法 [' . $tpl_method . ']', -99999);
				}
				break;
			default:
				throw new LeapException('LPF', '没有找到Controller的扩展模块 [' . $_switcher[0] . ']', -99999);
				break;
		}
	}

	/**
	 * 页面跳转，如未指定url参数，则跳转到当前页面的来源页面
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $url
	 */
	public function redirect($url = '') {
		$url = $url == '' ? filter_input(INPUT_SERVER, 'HTTP_REFERER') : $url;
		header(leapJoin('Location: ', $url));
		exit;
	}
}
