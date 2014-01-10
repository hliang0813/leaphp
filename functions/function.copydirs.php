<?php
leapCheckEnv();
/**
 * 遞歸複製目錄及目錄下全部內容
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $sourceDir
 * @param unknown $aimDir
 * @return boolean
 */
function leap_function_copydirs($sourceDir,$aimDir){
	$succeed = true;
	if(!file_exists($aimDir)){
		if(!mkdir($aimDir,0777)){
			return false;
		}
	}
	$objDir = opendir($sourceDir);
	while(false !== ($fileName = readdir($objDir))){
		if(($fileName != ".") && ($fileName != "..")){
			if(!is_dir("$sourceDir/$fileName")){
				if(!copy("$sourceDir/$fileName","$aimDir/$fileName")){
					$succeed = false;
					break;
				}
			} else{
				leap_function_copydirs("$sourceDir/$fileName","$aimDir/$fileName");
			}
		}
	}
	closedir($objDir);
	return $succeed;
}
