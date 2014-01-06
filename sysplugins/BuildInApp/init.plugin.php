<?php

// echo $page_head;

class BuildInApp {
	public function Install() {
// 		$file = _server('SCRIPT_FILENAME');
		require_once __DIR__ . '/AppInstall/AppInstall.php';
		AppInstall::Show();
	}
}