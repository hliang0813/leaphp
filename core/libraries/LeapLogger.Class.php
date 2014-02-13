<?php
leapCheckEnv();
/**
 * 日誌記錄與處理類
 * 
 * @author hliang
 * @package 
 * @subpackage 
 * @since 1.0.0
 *
 */
class LeapLogger extends Logger {
	static private $is_load_config = false;
	
	/**
	 * 加載與合併配置文件
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $configuration
	 * @param string $configurator
	 */
	public static function configure($configuration = null, $configurator = null) {
		if (!self::$is_load_config) {
			$default_config = array(
				'threshold' => 'ALL',
				'rootLogger' => array(
					'level' => 'ERROR',
					'appenders' => array('default'),
				),
				'appenders' => array(
					'default' => array(
						'class' => 'LoggerAppenderRollingFile',
						'layout' => array(
							'class' => 'LoggerLayoutPattern',
							'params' => array(
								'conversionPattern' => "%d{Y-m-d H:i:s} - %-5p - %c - %m - %F at %L%n",
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
			
			if ($configuration) {
				$configuration = LeapFunction('array_merge', $default_config, $configuration);
			}
			parent::configure($configuration, $configurator);
			self::$is_load_config = true;
		}
	}

	/**
	 * 獲取LOGGER操作句柄
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $name
	 * @return Ambigous <Logger, multitype:>
	 */
	public static function getLogger($name) {
		self::configure(LeapConfigure::get('logger'), 'LoggerConfiguratorPhp');
		return parent::getLogger($name);
	}
}
