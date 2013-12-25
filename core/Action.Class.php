<?php
class Action extends Base {
	static private $template;

	// 由其它模块扩展而来的功能
	public function __call($method, $params) {
		switch (explode('_', $method, 2)[0]) {
			case 'tpl':
				// 使用模板类中的方法
				$tpl_method = substr($method, 4);
				if (!is_object(self::$template)) {
					// 引入SMARTY模板子框架
					require_once leapJoin(__DIR__, DS, 'template', DS, 'Smarty.class.php');
					// 初始化SMARTY模板
					self::$template = new Smarty;
					// 模板文件夹
					self::$template->setTemplateDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'templates'));
					// 模板编译文件夹
					self::$template->setCompileDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'templates_c'));
					// 模板CACHE文件夹
					self::$template->setCacheDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'caches'));
					// 模板左右边界
					self::$template->setLeftDelimiter("<{");
					self::$template->setRightDelimiter("}>");
				}
				// 调用模板方法
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
