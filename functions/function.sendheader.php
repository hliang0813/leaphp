<?php
/**
 * 向瀏覽器發送http header頭
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $code
 */
function leap_function_sendheader($code) {
	switch($code) {
		case '404':
			header("HTTP/1.0 404 Not Found");
			die('<h2>Error 404 - Not Found</h2>');
			break;
	}
}
