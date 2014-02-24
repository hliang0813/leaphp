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
	public function __construct($module = '', $message = '', $code = 0, $previous = NULL) {
		$exception_message = self::leapMsg($module, $message);
		
		$_msg = array(
			'Code' => $code,
			'Module' => $module,
			'Message' => $message,
		);
		
		parent::__construct(JSON::encode($_msg), $code, $previous);
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
