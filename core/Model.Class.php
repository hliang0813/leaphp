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

	public function obj() {
		return $this->_object;
	}

	public function save(array $data) {
		$_o = $this->_object->create();
		$_o->set($data);
		return $_o->save();
	}

	public function delete($condition = NULL) {
		$_o = $this->_object;

		if (is_array($condition)) {
			// 传WHERE条件
			foreach ($condition as $key => $block) {
				list($_cond, $value) = explode(':', $block, 2);
				if (in_array($key, $this->_all_keys)) {
					$_where = 'where_' . $_cond;
					$_o = $_o->$_where($key, $value);
				}
			}
			return $_o->delete_many();
		} else if (is_numeric($condition)) {
			// 传主键ID
			$_o = $_o->find_one($condition);
			return $_o->delete();
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '错误的删除条件。'));
		}
	}

	public function update(array $data, $condition = NULL) {
		$_o = $this->_object;
		
		if (is_array($condition)) {
			// 传WHERE条件
			foreach ($condition as $key => $block) {
				list($_cond, $value) = explode(':', $block, 2);
				if (in_array($key, $this->_all_keys)) {
					$_where = 'where_' . $_cond;
					$_o = $_o->$_where($key, $value);
				}
			}
			$_o = $_o->find_one();
		} else if (is_numeric($condition)) {
			// 传主键ID
			$_o = $_o->find_one($condition);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '错误的查询条件。'));
		}

		$_o->set($data);
		return $_o->save();
	}
}
