<?php
leapCheckEnv();
/**
 * 框架中使用的异常类LeapException，从Exception类继承
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
class LeapException extends Exception {
	private $_msg;
	public function __construct($module = '', $message = '', $code = 0, $previous = NULL) {
		$this->_msg = array(
			'Code' => $code,
			'Module' => $module,
			'Message' => $message,
			'File' => $this->file,
			'Line' => $this->line,
		);
		
		$logger = LeapLogger::getLogger('LeapException');
		$logger->fatal(var_export($this->_msg, true));
		
		parent::__construct(JSON::encode($this->_msg), $code, $previous);
	}
	
	/**
	 * 生成显示给开发者及用户的异常信息
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $module
	 * @param string $message
	 * @return string
	 */
	static public function leapMsg($module = NULL, $message = NULL) {
		$message = array(
			'Message' => $message,
		);
		if (DEBUT) {
			$message['Module'] = $module;
		}
		return Base::response($message);
	}
}
