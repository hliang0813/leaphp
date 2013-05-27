<?php
// 引入SMARTY模板子框架
require_once __DIR__ . DS . 'template' . DS . 'Smarty.class.php';

class Action extends Base {
	static private $template;

	// public function __construct() {

	// }

	// 由其它模块扩展而来的功能
	public function __call($method, $params) {
		switch (explode('_', $method, 2)[0]) {
			case 'tpl':
				// 使用模板类中的方法
				$tpl_method = substr($method, 4);
				if (!is_object(self::$template)) {
					// 初始化SMARTY模板
					self::$template = new Smarty;
					// 模板文件夹
					self::$template->setTemplateDir(APP_ABS_PATH . DS . APP_NAME . DS . 'templates');
					// 模板编译文件夹
					self::$template->setCompileDir(APP_ABS_PATH . DS . APP_NAME . DS . 'templates_c');
					// 模板CACHE文件夹
					// self::$template->setCacheDir(APP_ABS_PATH . DS . APP_NAME . DS . 'caches');
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

	// # 模板变量赋值
	// public function assign($mark, $value) {
	// 	if ($this->use_template) {
	// 		$this->template->assign($mark, $value);
	// 	} else {
	// 		throw new Exception('Template(s) unactived.', 824200006);
	// 	}
	// }

	// # 显示模板
	// public function display($template_file = null) {
	// 	if ($this->use_template) {
	// 		if ($template_file == null) {
	// 			$template_file = ltrim(self::$controller . '/' . self::$action . ".html", '/');
	// 		}
	// 		$this->template->display($template_file);
	// 	} else {
	// 		throw new Exception('Template(s) unactived.', 824200006);
	// 	}
	// }

	# 页面跳转
	static protected function redirect($url = '') {
		$url = $url == '' ? $_SERVER['HTTP_REFERER'] : $url;
		echo '<script>window.location="' . $url . '";</script>';
		exit;
	}
}
