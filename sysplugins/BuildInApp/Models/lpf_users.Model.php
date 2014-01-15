<?php
class lpf_users extends Model {
	protected $id = 'u_id';
	protected $keys = array(
		'u_name' => 'varchar(50)',
		'u_pass' => 'varchar(50)',
		'u_uptime' => 'int(11) default 0',
		'u_lasttime' => 'int(11) default 0',
	);
}
