<?php
class Page {	
	private $_record_seek = 0;
	private $_record_limit = 10;
	
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
	
	public function p($limit = 10, $pid = 1) {
		$this->_record_limit = abs(intval($limit));
		$this->_record_seek = ($pid - 1) * $this->_record_limit;
		
		$_total_record = $this->getCounter()->result;
		
		$return = array(
			'info' => array(
				'total_record' => $_total_record,
				'total_page' => ceil($_total_record/$this->_record_limit),
				'current_page' => $pid,
			),
			'data' => $this->getList($pid)->result,
		);
		return Base::response((object)$return);
	}
	
	private function getList($pid) {
		$_data = $this->_object->limit($this->_record_limit)->offset($this->_record_seek)->find_array();
		return Base::response($_data);
	}
	
	private function getCounter() {
		$_counter = $this->_object->count();
		return Base::response($_counter);
	}
}