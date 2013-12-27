<?php
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
		if (DEBUG) {
			$message = leapJoin('BLOCK: ', $module, '; MESSAGE: ', $message);
		} else {
			$message = leapJoin('MESSAGE: ', $message);
		}
		return $message;
	}
}