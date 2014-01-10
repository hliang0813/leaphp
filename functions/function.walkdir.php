<?php
leapCheckEnv();
define('LEAP_WALKDIR_ALL', 0);
define('LEAP_WALKDIR_FOLDER', 1);
define('LEAP_WALKDIR_FILE', 2);

/**
 * 保存遍歷結果的靜態類
 * 
 * @author hliang
 * @package leaphp
 * @subpackage functions
 * @since 1.0.0
 *
 */
class WalkDirData {
	static private $dir_list = array();
	static public function push($dir) {
		return array_push(self::$dir_list, $dir);
	}
	static public function all() {
		return array_reverse(self::$dir_list);
	}
	static public function clear() {
		self::$dir_list = array();
		return true;
	}
}

/**
 * 遞歸遍歷目錄及其子目錄下的全部文件
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $dirname
 * @param unknown $with_folder
 * @param number $max_level
 * @return boolean|unknown
 */
function leap_function_walkdir($dirname, $with_folder, $max_level = 0){
	if(!file_exists($dirname)){
		return false;
	}
	$d = dir($dirname);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.' && $entry != '..') {
			if ($max_level > 0) {
				$_check_level_path = str_replace(APP_ABS_PATH . DS . 'input' . DS, '', $dirname . DS . $entry);
				if (count(explode(DS, $_check_level_path)) > $max_level) {
					continue;
				}
			}
				

			if (!is_dir($dirname . DS . $entry)) {
				if ($with_folder == 'all' || $with_folder == 'file') {
					WalkDirData::push(realpath($dirname . DS . $entry));
				}
			} else {
				leap_function_walkdir($dirname . DS . $entry, $with_folder, $max_level);
				if ($with_folder == 'all' || $with_folder == 'folder') {
					WalkDirData::push(realpath($dirname . DS . $entry));
				}
			}
		}	
	}
	$d->close();
	$files = WalkDirData::all();
	return $files;
}
