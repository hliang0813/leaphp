<?php
require_once __DIR__ . '/idiorm.php';

class LeapORM extends ORM {
	static public function configure($selecter = 'master', $confiure = 'database') {
		parent::configure(LeapDB::configure($selecter, $confiure));
	}
}
