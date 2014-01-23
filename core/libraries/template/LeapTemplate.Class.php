<?php
require_once leapJoin(__DIR__, DS, 'Smarty.class.php');
class LeapTemplate extends Smarty {
	public function __construct() {
		parent::__construct();
		// 初始化SMARTY模板引擎
		parent::setTemplateDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'templates'));
		parent::setCompileDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'templates_c'));
		parent::setCacheDir(leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'caches'));
		parent::setLeftDelimiter('<{');
		parent::setRightDelimiter('}>');
	}
	
	public function display($template=null, $cache_id=null, $compile_id=null, $parent=null) {
		if (!$template) {
			$template = leapJoin(App::getController()->result, DS, App::getAction()->result, '.html');
		}
		
		parent::display($template, $cache_id, $compile_id, $parent);
	}
}