<?php
class Page {	
	private $_record_seek = 0;
	private $_record_limit = 10;
	
	private $_current_page = 0;
	private $_total_page = 0;
	private $_lookup_page = 5;
	
	private $_object = NULL;
	
	public function __construct($model) {
		if (!$model instanceof Model) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '参数类型错误。'));
		}

		$this->_object = $model->obj();
	}
	
	public function __call($method, $params) {
		call_user_func_array(array($this->_object, $method), $params);
		return $this;
	}
	
	public function page($pid = 1, $limit = 10, $lookup = 5) {
		$this->_record_limit = abs(intval($limit));
		$this->_record_seek = ($pid - 1) * $this->_record_limit;
		
		$_total_record = $this->getCounter()->body;
		
		$this->_current_page = $pid;
		$this->_total_page = ceil($_total_record/$this->_record_limit);
		$this->_lookup_page = $lookup;
		
		$return = array(
			'info' => (object)array(
				'total_record' => $_total_record,
				'total_page' => $this->_total_page,
				'current_page' => $this->_current_page,
				'page_record' => abs(intval($limit)),
				'page_list' => $this->getPageList(),
			),
			'data' => $this->getDataList($this->_current_page)->body,
		);
		return (object)$return;
	}
	
	private function getDataList($pid) {
		$_data = $this->_object->limit($this->_record_limit)->offset($this->_record_seek)->find_array();
		return Base::response($_data);
	}
	
	private function getCounter() {
		$_counter = $this->_object->count();
		return Base::response($_counter);
	}
	
	private function getPageList() {
		$_pagelist_ary = array();
	
		if ($this->_current_page > $this->_total_page) {
			return $_pagelist_ary;
		}
	
		for ($i = $this->_current_page - $this->_lookup_page; $i <= $this->_current_page + $this->_lookup_page; $i ++) {
			if ($i <= 0) {
				continue;
			}
			array_push($_pagelist_ary, $i);
			if ($i >= $this->_total_page) {
				break;
			}
		}
	
		if ($_pagelist_ary[0] > 1) {
			array_unshift($_pagelist_ary, '...');
			array_unshift($_pagelist_ary, '1');
		}
	
		if ($_pagelist_ary[count($_pagelist_ary) - 1] < $this->_total_page) {
			array_push($_pagelist_ary, '...');
			array_push($_pagelist_ary, $this->_total_page);
		}
	
		return $_pagelist_ary;
	}
}