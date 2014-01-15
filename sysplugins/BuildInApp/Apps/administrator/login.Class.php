<?php
class login extends Controller {
	// 判断当前登录状态
	static public function chkLoginState() {
		if ($_SESSION['lpfba_administrator']) {
			return $_SESSION['lpfba_administrator'];
		} else {
			return false;
		}
	}

	// 修改当前登录状态
	static private function changeLoginState($user) {
		if ($user) {
			$_SESSION['lpfba_administrator'] = $user;
		} else {
			session_destroy();
		}
		return true;
	}

	// 登录功能路由
	public function init() {
		$act = filter_input(INPUT_GET, 'act');
		if ($act) {
			self::$act();
		} else {
			self::loginPage();
		}
		
// 		BuildInCommon::initDb();
	}

	// 登录页面
	public function loginPage() {
		if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
			$username = filter_input(INPUT_POST, 'username');
			$password = filter_input(INPUT_POST, 'password');
			
			LeapImport::file(SYSPLUGIN_DIR . DS . 'BuildInApp' . DS . 'Models' . DS . 'lpf_users.Model.php');

			ORM::configure(LeapDB::configure('master', 'administrator'));
			$user = new lpf_users();
			
			ORM::get_db()->beginTransaction();
			
			$login_result = $user->obj()->where_equal('u_name', $username)->where_equal('u_pass', md5($password))->find_one();
			$upd_result = $user->update(array(
					'u_lasttime' => mktime(),
			), $login_result->id());
			
			
// 			// 初始化数据库对象
			
// 			// 开启事务
// // 			$db->beginTransaction();
// 			// 执行登录查询
// 			$user = $db->execute('SELECT u_id, u_name, u_uptime, u_lasttime FROM lpfba_users WHERE u_name = ? AND u_pass = ?', array(
// 				$username, md5($password),
// 			))[0];
// 			// 修改最后登录时间
// 			$db->execute('UPDATE lpfba_users SET u_lasttime = ? WHERE u_name = ?', array(
// 				mktime(), $username,
// 			));

// 			// 如果登录成功，提交事务
// 			if ($user) {
// 				$result = $db->commit();
// 			}

			// 如果事务提交成功，修改登录状态
// 			if ($result && self::changeLoginState($user)) {
// 				self::redirect(leapJoin(ENTRY_URI, '/administrator/main/'));
// 			} else {
// 				self::redirect();
// 			}

			exit;
		}
		// 模板展示
		self::tpl_display(leapJoin(BUILDINAPP_TPL_PATH, DS, 'administrator', DS, 'login.html'));
	}

	// 注销登录
	public function logout() {
		if (self::changeLoginState(array())) {
			self::redirect(leapJoin(ENTRY_URI, '/administrator/login/'));
		} else {
			self::redirect();
		}
	}
}
