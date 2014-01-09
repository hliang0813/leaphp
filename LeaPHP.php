<?php
// 限制PHP版本号
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
	die('LeaPHP requires PHP 5.4.0 or higher');
}
// 框架版本号
define('LEAPHP_VERSION_ID', '1.0.0');
define('LEAPHP_VERSION_RELEASE', 'alpha');

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
defined('CONFIG_DIR') or define('CONFIG_DIR', leapJoin(APP_ABS_PATH, DS, 'configs'));
// 设置框架系统插件目录
defined('SYSPLUGIN_DIR') or define('SYSPLUGIN_DIR', leapJoin(__DIR__, DS, 'sysplugins'));
// 设置控制器文件相对目录ACTION_DIR
defined('CONTROLLER_DIR') or define('CONTROLLER_DIR', leapJoin(APP_ABS_PATH, DS, APP_NAME, DS, 'controllers'));
// 设置业务类目录
defined('BUSINESS_DIR') or define('BUSINESS_DIR', leapJoin(APP_ABS_PATH, DS, 'business'));
// 设置ORM对象目录
// defined('ORM_DIR') or define('ORM_DIR', 'models');
// 设置CACHE目录
// defined('CACHE_DIR') or define('CACHE_DIR', 'caches');

require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'log4php', DS, 'Logger.php');
spl_autoload_register('leapAutoload');
set_exception_handler('leapException');

// 加载配置文件
if (file_exists(CONFIGURE)) {
	require_once CONFIGURE;
	if (is_array($config)) {
		LeapConfigure::load($config);
	}
}

$logger = LeapLogger::getLogger('lpf_mainloop');
$logger->trace('成功加载主框架文件。');

// LeaPHP 统一捕获异常
function leapException($e) {
	$error = sprintf('[ERROR #%d] %s', $e->getCode(), $e->getMessage());
	echo $error;
	if (DEBUG) {
		echo leapJoin('<div style="font-size:13px;"><pre>', $e->getTraceAsString(), '</div>');
	}
	exit();
}

$logger->trace('开始引入主框架必要文件。');
// 引入框架文件
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapException.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Plugins.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'Params.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, 'LeapCache.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Base.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Copyright.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'App.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Controller.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Dispatch.Class.php');
$logger->trace('成功引入主框架必要文件。');

// 自动装载类库
function leapAutoload($class_name) {
	$library_file = leapJoin(__DIR__, DS, 'core', DS, 'libraries', DS, $class_name, '.Class.php');
	$sysplugin_file = leapJoin(SYSPLUGIN_DIR, DS, $class_name, DS, 'init.plugin.php');
	$business_file = leapJoin(BUSINESS_DIR, DS, $class_name, '.Class.php');
	
	if (file_exists($library_file)) {
		// 自动加载框架内部库
		require_once $library_file;
	} elseif (file_exists($sysplugin_file)) {
		// 自动加载框架内部插件
		require_once $sysplugin_file;
	} elseif (file_exists($business_file)) {
		// 自动加载业务类
		require_once $business_file;
	}
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
