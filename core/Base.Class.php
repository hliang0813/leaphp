<?php
leapCheckEnv();
/**
 * Base类是框架全部其它类的基类
 * 
 * @author hliang
 * @package leaphp
 * @subpackage 
 * @since 1.0.0
 *
 */
class Base {
	/**
	 * 判断一个字符串$string是否以另一个字符串$substring开头
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $string
	 * @param string $substring
	 * @return boolean
	 */
	static public function startWith($string, $substring) {
		if (substr($string, 0, strlen($substring)) == $substring) {
			return true;
		} else {
			return false;
		}
	}
	
	static public function response($result, $error = NULL) {
		return (object)array(
				'error' => $error,
				'result' => $result,
		);
	}
}
