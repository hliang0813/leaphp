<?php
require_once leapJoin(__DIR__, DS, 'log4php', DS, 'Logger.php');

class LeapLogger extends Logger {
	static private $is_load_config = false;
	
	public static function configure($configuration = null, $configurator = null) {
		if (!self::$is_load_config) {
			$default_config = array(
				'threshold' => 'ALL',
				'rootLogger' => array(
					'level' => 'INFO',
					'appenders' => array('default'),
				),
				'appenders' => array(
					'default' => array(
						'class' => 'LoggerAppenderRollingFile',
						'layout' => array(
							'class' => 'LoggerLayoutPattern',
							'params' => array(
								'conversionPattern' => "%d{Y-m-d H:i:s} - %-5p - %c - %X{username}: %m in %F at %L%n",
							),
						),
						'params' => array(
							'maxFileSize' => '10MB',
							'maxBackupIndex' => '5',
							'file' => leapJoin(APP_ABS_PATH, '/logs/', APP_NAME, '.log'),
						),
					),
				),
			);
			
			$configuration = array_merge($default_config, $configuration);
			
			parent::configure($configuration, $configurator);
			self::$is_load_config = true;
		}
	}

	public static function getLogger($name) {
		self::configure(LeapConfigure::get('logger'), 'LoggerConfiguratorPhp');
		return parent::getLogger($name);
	}
}