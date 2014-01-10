<?php
leapCheckEnv();
require_once leapJoin(__DIR__, DS, 'Requests.php');

/**
 * 從Requests包繼承來的HTTPClient
 * 
 * @author hliang
 * @package leaphp 
 * @subpackage sysplugins
 * @since 1.0.0
 *
 */
class HTTPClient extends Requests {
	
}

HTTPClient::register_autoloader();
