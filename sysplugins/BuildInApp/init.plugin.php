<?php
leapCheckEnv();
class BuildInApp extends Controller {
	public function administrator() {
		self::tpl_assign('aaa', 'bbb');
		self::tpl_display(leapJoin(__DIR__ . DS . 'templates' . DS . 'administrator.html'));
	}
}
