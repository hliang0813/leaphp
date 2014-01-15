<?php
class BuildInCommon {
	static public function initDb() {
		ORM::configure(LeapDB::configure('master', 'administrator'));
	}
}
