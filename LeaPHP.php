<?php
// 限制PHP版本号
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
	die('LeaPHP requires PHP 5.3.0 or higher');
}
// 框架版本号
define('LEAPHP_VERSION_ID', '1.0.0');
define('LEAPHP_VERSION_RELEASE', 'Stable');

// 指定必要的HEADER内容
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

// 设置默认DEBUG开关
defined('DEBUG') or define('DEBUG', false);
if (DEBUG) {
	error_reporting(7);
} else {
	error_reporting(0);
}

// 设置系统分隔符
define ('DS', DIRECTORY_SEPARATOR);

// 设置应用APP名称
defined('APP_NAME') or define('APP_NAME', 'leapapp');
// 设置框架本身的绝对路径
define('LEAP_ABS_PATH', __DIR__);
// 设置应用的绝对路径
define('APP_ABS_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')));

defined('CONFIGURE') or define('CONFIGURE', leapJoin(APP_ABS_PATH, DS, 'configs', DS, 'config.ini.php'));
// 设置配置文件相对目录CONFIG_DIR
// defined('CONFIG_DIR') or define('CONFIG_DIR', leapJoin(APP_ABS_PATH, DS, 'configs'));
// 设置框架系统插件目录
defined('SYSPLUGIN_DIR') or define('SYSPLUGIN_DIR', leapJoin(__DIR__, DS, 'sysplugins'));
// 设置控制器文件相对目录ACTION_DIR
defined('CONTROLLER_DIR') or define('CONTROLLER_DIR', leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'controllers'));
// 设置数据模型目录
defined('MODEL_DIR') or define('MODEL_DIR', leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'models'));
// 设置业务类目录
defined('BUSINESS_DIR') or define('BUSINESS_DIR', leapJoin(APP_ABS_PATH, DS, 'businesses'));

// 引入日志模块文件
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'log4php', DS, 'Logger.php');
spl_autoload_register('leapAutoload');
set_exception_handler('leapException');
// set_error_handler('leapError');

// 加载配置文件
if (file_exists(CONFIGURE)) {
	require_once CONFIGURE;
	if (is_array($config)) {
		LeapConfigure::load($config);
	}
}

function leapError($errno, $errstr, $errfile, $errline, $errcontext) {
	switch ($errno) {
		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
		case E_USER_NOTICE:
			return true;
			break;
		default:
			die('asdfasdf');
			break;
	}
}

// LeaPHP 统一捕获异常
function leapException($e) {
	$_message = JSON::decode($e->getMessage());
	if ($_message->error === NULL) {
		$exception_msg = JSON::encode(Base::response($_message->body, $e->getCode()));
	} else {
		$exception_msg = $e->getMessage();
	}
	if (DEBUG) {
		echo $exception_msg;
	}
}

// 引入框架文件
require_once leapJoin(__DIR__, DS, 'core', DS, 'Base.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapException.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Plugins.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Params.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapCache.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Copyright.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'App.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Controller.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Dispatch.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Model.Class.php');

// 自动装载类库
function leapAutoload($class_name) {
	$model_file = leapJoin(MODEL_DIR, DS, $class_name, '.Model.php');
	$library_file = leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, $class_name, '.Class.php');
	$sysplugin_file = leapJoin(SYSPLUGIN_DIR, DS, $class_name, DS, 'init.plugin.php');
	$business_file = leapJoin(BUSINESS_DIR, DS, $class_name, '.Class.php');

	if (file_exists($model_file)) {
		// 自动加载数据模型类
		require_once $model_file;
	} else if (file_exists($library_file)) {
		// 自动加载框架内部库
		require_once $library_file;
	} else if (file_exists($sysplugin_file)) {
		// 自动加载框架内部插件
		require_once $sysplugin_file;
	} else if (file_exists($business_file)) {
		// 自动加载业务类
		require_once $business_file;
	}
}

// 连接字符串，效率高于使用.来连接
function leapJoin() {
	return join(func_get_args());
}

// 检测框架插件及类库是否在框架外部使用
function leapCheckEnv() {
	if (!defined('LEAPHP_VERSION_ID')) {
		throw new LeapException(__METHOD__, '不能在框架外部使用LeaPHP的类库或插件', -99999);
	}
}

// 自动装载函数库
function LeapFunction() {
	$params = func_get_args();
	switch (func_num_args()) {
		case 0:
			throw new LeapException(__METHOD__, '使用自动加载的函数，需要至少传递一个参数', -99999);
		default:
			$function_name = leapJoin('leap_function_', $params[0]);
			if (!function_exists($function_name)) {
				$function_file = leapJoin(__DIR__, DS, 'functions', DS, 'function.', $params[0], '.php');
				if (file_exists($function_file)) {
					require_once $function_file;
				} else {
					throw new LeapException(__METHOD__, '没有找到需要自动加载函数的文件 [' . $function_file . ']', -99999);
				}
			} else {
				throw new LeapException(__METHOD__, '已经显示加载了指定的函数 [' . $function_name . ']', -99999);
			}
			unset($params[0]);
			return call_user_func_array($function_name, $params);
	}
}
