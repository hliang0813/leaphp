<?php
class Base {
	// 检查一字符串是否以另一字符串开头
	static protected function startWith($str, $match) {
		if (substr($str, 0, strlen($match)) == $match) {
			return true;
		} else {
			return false;
		}
	}

	# 加载数据库配置文件
	static protected function configure($config) {
		$config_file = realpath(CONFIG_DIR . DS . 'configure.ini');
		if (file_exists($config_file)) {
			return parse_ini_file($config_file, true)[$config];
		} else {
			throw new Exception("Error on loading database configuration file.", 824209001);
		}
	}
}
