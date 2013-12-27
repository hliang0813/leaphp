<?php
// 限制PHP版本号
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
	die('LeaPHP requires PHP 5.4.0 or higher');
}
// 框架版本号
define('LEAPHP_VERSION_ID', '1.0.0');
define('LEAPHP_VERSION_RELEASE', 'alpha');

// 指定必要的HEADER内容
// 设置默认DEBUG开关
defined('DEBUG') or define('DEBUG', false);
if (DEBUG) {
	error_reporting(7);
} else {
	error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');


// 设置系统分隔符
define ('DS', DIRECTORY_SEPARATOR);
// 设置框架本身的绝对路径
define('LEAP_ABS_PATH', __DIR__);
// 设置应用的绝对路径
define('APP_ABS_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')));

// 设置框架系统插件目录
defined('SYSPLUGIN_DIR') or define('SYSPLUGIN_DIR', leapJoin(__DIR__, DS, 'sysplugins'));
// 设置应用APP名称
defined('APP_NAME') or define('APP_NAME', 'leapapp');
// 设置配置文件相对目录CONFIG_DIR
defined('CONFIG_DIR') or define('CONFIG_DIR', 'configs');
// 设置控制器文件相对目录ACTION_DIR
defined('CONTROLLER_DIR') or define('CONTROLLER_DIR', 'controllers');
// 设置业务类目录
defined('BIZ_DIR') or define('BIZ_DIR', 'business');
// 设置ORM对象目录
defined('ORM_DIR') or define('ORM_DIR', 'models');
// 设置CACHE目录
defined('CACHE_DIR') or define('CACHE_DIR', 'caches');

// LeaPHP 统一捕获异常
function leapException($e) {
	$error = sprintf('[ERROR #%d] %s', $e->getCode(), $e->getMessage());
	echo $error;
	if (DEBUG) {
		echo leapJoin('<div style="font-size:13px;"><pre>', $e->getTraceAsString(), '</div>');
	}
	exit();
}
set_exception_handler('leapException');

// 引入框架文件
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapException.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Plugins.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Params.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapCache.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Base.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Copyright.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'App.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Controller.Class.php');


// 自动装载类库
function leapAutoload($class_name) {
	// 框架库文件
	$library_file = leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, $class_name, '.Class.php');
	if (file_exists($library_file)) {
		require_once $library_file;
	} else {
		// 框架插件入口文件
		$sysplugin_file = leapJoin(__DIR__, DS, 'sysplugins', DS, $class_name, DS, 'init.plugin.php');
		if (file_exists($sysplugin_file)) {
			require_once $sysplugin_file;
		}
	}
}
spl_autoload_register('leapAutoload');

// 加载配置文件
if (file_exists($config_file = leapJoin(APP_ABS_PATH, DS, CONFIG_DIR, DS, 'config.ini.php'))) {
	require_once $config_file;
	LeapConfigure::load($config);
}


// 文件的安全限制，不允许框架文件单独使用
function leapLimit() {
	return true;
}

// 连接字符串，效率高于使用.来连接
function leapJoin() {
	return join(func_get_args());
}

// 自动装载函数库
function LeapFunction() {
	$params = func_get_args();
	switch (func_num_args()) {
		case 0:
			throw new Exception('Parameter(s) error while using autoload function(s).', 824209015);
		default:
			$function_name = leapJoin('leap_function_', $params[0]);
			if (!function_exists($function_name)) {
				$function_file = leapJoin(__DIR__, DS, 'functions', DS, 'function.', $params[0], '.php');
				if (file_exists($function_file)) {
					require_once $function_file;
				} else {
					throw new Exception('Autoload function(s) not found.', 824209016);
				}
			}
			unset($params[0]);
			return call_user_func_array($function_name, $params);
	}
}
