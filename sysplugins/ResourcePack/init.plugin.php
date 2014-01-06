<?php

function _ResourcePack_autoload($clsName) {
	$_class_file = leapJoin(__DIR__, DS, str_replace('\\', DS, $clsName), '.php');
	if (file_exists($_class_file)) {
		require_once $_class_file;
	}
}
spl_autoload_register('_ResourcePack_autoload');


use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

class ResourcePack extends Base {
	static private function pack($script) {
		$_resources = array();
		foreach ((array)$script as $src) {
			array_push($_resources, new FileAsset(leapJoin(APP_ABS_PATH, $src)));
		}
		$js = new AssetCollection($_resources);
		
		return $js->dump();
	}
	
	static public function JS() {
		$res['scripts'] = func_get_args();
		if (!empty($res['scripts'])) {
			$_params = array();
			foreach ($res['scripts'] as $leaf) {
				array_push($_params, leapJoin('script[]=', $leaf));
			}
			$_resource_uri = leapJoin(ENTRY_URI, '/buildin/resource.js?', implode('&', $_params));
			$_output = leapJoin('<script type="text/javascript" src="', $_resource_uri, '"></script>');
			return $_output;
		}
	}
	
	static public function CSS() {
		$scripts = func_get_args();
		$_output = '<style>';
		$_output .= self::pack($scripts);
		$_output .= '</style>';
		return $_output;
	}
	
	static public function webInterface() {
		die(self::pack($_GET['script']));
	}
}