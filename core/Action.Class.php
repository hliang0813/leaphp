<?php
class Action extends Base {
	static private $template;

	// 魔术方法
	public function __call($method, $params) {
		switch (explode('_', $method, 2)[0]) {
			case 'tpl':
				// 根据前缀来判断调用何种方法
				$tpl_method = substr($method, 4);
				if (!is_object(self::$template)) {
					// 引入模板类
					require_once leapJoin(__DIR__, DS, 'libraries', DS, 'template', DS, 'LeapTemplate.Class.php');
					// 初始化模板对象
					self::$template = new LeapTemplate;
				}
				// 动态调用方法
				call_user_func_array(array(self::$template, $tpl_method), $params);
				break;
			default:
				die('method not found');
				break;
		}
	}

	// 页面跳转
	static protected function redirect($url = '') {
		$url = $url == '' ? filter_input(INPUT_SERVER, 'HTTP_REFERER') : $url;
		header(leapJoin('Location: ', $url));
		exit;
	}
}
