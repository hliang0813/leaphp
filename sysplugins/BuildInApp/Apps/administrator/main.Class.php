<?php
require_once __DIR__ . DS . 'login.Class.php';
class main extends Controller {
	// 主控制面板路由器
	public function init() {
		// 检测登录状态
		if (!login::chkLoginState()) {
			self::redirect(ENTRY_URI . '/administrator/login/');
		}

		self::tpl_display(leapJoin(BUILDINAPP_TPL_PATH, DS, 'administrator', DS, 'main_frame.html'));
	}
}
