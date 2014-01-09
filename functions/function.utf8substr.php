<?php
/**
 * UTF8字符串截取
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $str
 * @param unknown $start
 * @param unknown $len
 */
function leap_function_utf8substr($str, $start, $len) {
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}'. 
	'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', 
	'$1',$str); 
}
