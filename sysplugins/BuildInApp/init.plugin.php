<?php
leapCheckEnv();
define('BUILDINAPP_TPL_PATH', leapJoin(__DIR__, DS, 'templates'));
define('BUILDINAPP_CLS_PATH', leapJoin(__DIR__, DS, 'Apps', DS, 'administrator'));
require_once leapJoin(__DIR__, DS, 'Apps', DS, 'BuildInCommon.Class.php');
class BuildInApp {
	public function administrator($module) {
		session_start();
		$cls_file = leapJoin(BUILDINAPP_CLS_PATH, DS, $module, '.Class.php');
		if (file_exists($cls_file)) {
			require_once $cls_file;
			call_user_func_array(array($module, 'init'), array());
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Administrator Framework method does not found.'));
		}
	}
}
