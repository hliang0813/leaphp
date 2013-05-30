<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
	die('PHP ActiveRecord requires PHP 5.3 or higher');

define('PHP_ACTIVERECORD_VERSION_ID','1.0');

if (!defined('PHP_ACTIVERECORD_AUTOLOAD_PREPEND'))
	define('PHP_ACTIVERECORD_AUTOLOAD_PREPEND',true);

require_once __DIR__ . '/activerecord/Singleton.php';
require_once __DIR__ . '/activerecord/Config.php';
require_once __DIR__ . '/activerecord/Utils.php';
require_once __DIR__ . '/activerecord/DateTime.php';
require_once __DIR__ . '/activerecord/Model.php';
require_once __DIR__ . '/activerecord/Table.php';
require_once __DIR__ . '/activerecord/ConnectionManager.php';
require_once __DIR__ . '/activerecord/Connection.php';
require_once __DIR__ . '/activerecord/SQLBuilder.php';
require_once __DIR__ . '/activerecord/Reflections.php';
require_once __DIR__ . '/activerecord/Inflector.php';
require_once __DIR__ . '/activerecord/CallBack.php';
require_once __DIR__ . '/activerecord/Exceptions.php';
require_once __DIR__ . '/activerecord/Cache.php';

if (!defined('PHP_ACTIVERECORD_AUTOLOAD_DISABLE'))
	spl_autoload_register('activerecord_autoload',false,PHP_ACTIVERECORD_AUTOLOAD_PREPEND);

function activerecord_autoload($class_name)
{
	$path = ActiveRecord\Config::instance()->get_model_directory();
	$root = realpath(isset($path) ? $path : '.');

	if (($namespaces = ActiveRecord\get_namespaces($class_name)))
	{
		$class_name = array_pop($namespaces);
		$directories = array();

		foreach ($namespaces as $directory)
			$directories[] = $directory;

		$root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
	}

	$file = "$root/$class_name.php";

	if (file_exists($file))
		require_once $file;
}


class Model extends Base {
	static public function init($config) {
		$config = Base::configure('Model_' . $config);
		if ($config['db_pass'] == '') {
			$connect_string = sprintf('%s://%s@%s:%s/%s;charset=%s', $config['db_driver'], $config['db_user'], $config['db_server'], $config['db_port'], $config['db_name'], $config['db_charset']);
		} else {
			$connect_string = sprintf('%s://%s:%s@%s:%s/%s;charset=%s', $config['db_driver'], $config['db_user'], $config['db_pass'], $config['db_server'], $config['db_port'], $config['db_name'], $config['db_charset']);
		}

		$connections = array(
			'development' => $connect_string,
		);
		ActiveRecord\Config::initialize(function($cfg) use ($connections)
		{
			$cfg->set_model_directory(APP_ABS_PATH . DS . APP_NAME . DS . ORM_DIR);
			$cfg->set_connections($connections);
		});
	}
}
