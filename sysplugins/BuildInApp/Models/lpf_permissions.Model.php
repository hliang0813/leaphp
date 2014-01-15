<?php
class lpf_permissions extends Model {
	protected $id = 'permission_id';
	protected $keys = array(
		'u_id' => 'int(11) default 0',
		'permission_list' => 'text',
	);
}