<?php
leapCheckEnv();
define('BUILDINAPP_TPL_PATH', leapJoin(__DIR__, DS, 'templates'));
define('BUILDINAPP_CLS_PATH', leapJoin(__DIR__, DS, 'apps', DS, 'administrator'));
class BuildInApp extends Controller {
	public function administrator($module) {
		self::tpl_assign('aaa', $module);
		self::tpl_display(leapJoin(BUILDINAPP_TPL_PATH, DS, 'administrator.html'));
	}
}
