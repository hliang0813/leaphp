<?php
leapCheckEnv();
/**
 * 按層級創建目錄
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $dir
 * @return boolean
 */
function leap_function_mkdirs($dir) {
	if(!is_dir($dir)) {
		if(!leap_function_mkdirs(dirname($dir))) {
			return false;
		}
		if(!mkdir($dir,0777)) {
			return false;
		} else {
			chmod($dir, 0777);
		}
	}
	return true;
}
