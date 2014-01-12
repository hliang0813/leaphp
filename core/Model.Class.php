<?php
class Model {
	protected $primary_key = 'id';
	protected $keys = array();
	
	private $_table = NULL;
	private $_all_keys = array();
	private $_object = NULL;

	public function __construct() {
		if (!$this->primary_key) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '需要为数据模型指定一个主键。'));
		}
		if (!($this->keys)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '需要为数据模型指定字段列表。'));
		}
		$this->_table = get_class($this);
		$this->_all_keys = array_merge($this->keys, (array)$this->primary_key);
		$this->_object = ORM::for_table($this->_table)->use_id_column($this->primary_key);
	}

	public function save(array $data) {
		$_o = $this->_object->create();
		foreach ($data as $key => $value) {
			if (in_array($key, $this->_all_keys)) {
				$_o->$key = $value;
			}
		}
		return $_o->save();
	}

	public function delete(array $data) {
		$_o = $this->_object;
		foreach ($data as $key => $block) {
			list($condition, $value) = explode(':', $block, 2);
			if (in_array($key, $this->_all_keys)) {
				$_where = 'where_' . $condition;
				$_o = $_o->$_where($key, $value);
			}
		}
		return $_o->delete_many();
		
	}

	public function update(array $data, array $cond) {
		$_o = $this->_object;
		foreach ($cond as $key => $block) {
			list($condition, $value) = explode(':', $block, 2);
			if (in_array($key, $this->_all_keys)) {
				$_where = 'where_' . $condition;
				$_o = $_o->$_where($key, $value);
			}
		}
		$_o = $_o->find_one();
		foreach ($data as $key => $value) {
			if (in_array($key, $this->_all_keys)) {
				$_o->$key = $value;
			}
		}
		return $_o->save();
	}

	public function fetch() {

	}
}
