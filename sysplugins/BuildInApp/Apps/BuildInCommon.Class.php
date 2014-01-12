<?php
class BuildInCommon {
	static private $db = NULL;
	static public function initDb() {
		$logger = LeapLogger::getLogger(__METHOD__);

		if (!is_object(self::$db)) {
			$logger->info('初始化数据库对象。');
			self::$db = new LeapDB('master', 'administrator');
		}
		return self::$db;
	}
}
