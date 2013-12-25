<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
	die('LeaPHP requires PHP 5.4 or higher');
}
define('LEAPHP_VERSION_ID', '1.0.0');
define('LEAPHP_VERSION_RELEASE', 'alpha');

error_reporting(0);
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
// 设置默认DEBUG开关
defined('DEBUG') or define('DEBUG', false);
// 设置应用APP名称
defined('APP_NAME') or define('APP_NAME', 'leapapp');
// 设置配置文件相对目录CONFIG_DIR
defined('CONFIG_DIR') or define('CONFIG_DIR', 'configs');
// 设置控制器文件相对目录ACTION_DIR
defined('ACTION_DIR') or define('ACTION_DIR', 'actions');
// 设置业务类目录
defined('BIZ_DIR') or define('BIZ_DIR', 'business');
// 设置ORM对象目录
defined('ORM_DIR') or define('ORM_DIR', 'models');
// 设置CACHE目录
defined('CACHE_DIR') or define('CACHE_DIR', 'caches');

// 加载配置文件
$config_file = leapJoin(APP_ABS_PATH, DS, CONFIG_DIR, DS, 'config.ini.php');
if (file_exists($config_file)) {
	require_once $config_file;
}

// 引入框架文件
require_once leapJoin(__DIR__, DS, 'core', DS, 'Base.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Copyright.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'App.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'Action.Class.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'library', DS, 'Params.Function.php');
require_once leapJoin(__DIR__, DS, 'core', DS, 'library', DS, 'LeapCache.Class.php');

# LeaPHP 统一异常处理
function LeaphpException($e) {
	$error = sprintf('[ERROR #%d] %s', $e->getCode(), $e->getMessage());
	echo $error;
	if (DEBUG) {
		echo '<div style="font-size:13px;"><pre>', $e->getTraceAsString(), '</div>';
	}
	exit();
}
set_exception_handler('LeaphpException');


# 自动装载类库
function LeapClassAutoload($class_name) {
	# 根据leaphp的目录结构设置自动装载的位置
	$c_map = array(
		'Model'			=> '',
		'DataBase'		=> leapJoin('db', DS),
		'Db'			=> leapJoin('db', DS),
		'MasterSlave'	=> leapJoin('db', DS),
		'PageNav'		=> leapJoin('library', DS),
	);
	if (array_key_exists($class_name, $c_map)) {
		$class_file = leapJoin(__DIR__, DS, 'core', DS, $c_map[$class_name], $class_name, '.Class.php');
	} else {
		$class_file = leapJoin(__DIR__, DS, 'sysplugins', DS, $class_name, DS, 'init.plugin.php');
	}
	if (file_exists($class_file)) {
		require_once $class_file;
	}
}
spl_autoload_register('LeapClassAutoload');

function visit_limit() {
	// if (!defined('LEAP_START')) {
	// 	LeapFunction('sendheader', 404);
	// }
}

function leapJoin() {
	return join(func_get_args());
}

# 自动装载函数库
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

//# 注册HOOK函数
//function LeapHook_register($hookname, $callback) {
//	$GLOBALS['_LEAP_HOOK'][$hookname][] = $callback;
//}
//
//# 调用HOOK函数
//function LeapHook_add($hookname, $data) {
//	foreach ((array)$GLOBALS['_LEAP_HOOK'][$hookname] as $hook) {
//		return $hook($data);
//	}
//	return $data;
//}


