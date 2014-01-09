<?php
/**
 * 多维数组合并
 */
function leap_function_array_merge ($array1, $array2) {
	if (is_array($array2) && count($array2)) {
		foreach ($array2 as $k => $v) {
			if (is_array($v) && count($v)) {
				$array1[$k] = leap_function_array_merge($array1[$k], $v);
			} else {
				if(!empty($v)) {
					$array1[$k] = $v;
				}
			}
		}
	} else {
		$array1 = $array2;
	}
	return $array1;
}