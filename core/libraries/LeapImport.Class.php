<?php
/**
 * 引入文件
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
class LeapImport extends Base {
	/**
	 * 引入业务类文件
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $biz_name
	 */
	static public function biz($biz_name) {
		$biz_file = leapJoin(BUSINESS_DIR, DS, $biz_name, '.Class.php');
		if (file_exists($biz_file)) {
			require_once $biz_file;
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "没有找到对应的业务类文件 [{$biz_name}]。"));
		}
	}
	
	/**
	 * 引入模型类文件
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $model_name
	 */
	static public function model($model_name) {
		$model_file = leapJoin(MODEL_DIR, DS, $model_name, '.Model.php');
		if (file_exists($model_file)) {
			require_once $model_file;
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "没有找到对应的模型文件 [{$model_name}]。"));
		}
	}
	
	/**
	 * 引入普通文件
	 *
	 * @author hliang
	 * @since 1.0.0
	 *
	 * @param string $model_name
	 */
	static public function file($file) {
		if (file_exists($file)) {
			require_once $file;
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "没有找到引入的文件 [{$file}]。"));
		}
	}
}