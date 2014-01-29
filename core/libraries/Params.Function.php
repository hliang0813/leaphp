<?php
leapCheckEnv();
/**
 * 封裝的$_GET超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _get($key, $default = NULL) {
	$_get = filter_input(INPUT_GET, $key);
	return $_get == NULL ? $default : $_get;
}

/**
 * 封裝的$_POST超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _post($key, $default = NULL) {
	$_post = filter_input(INPUT_POST, $key);
	return $_post == NULL ? $default : $_post;
}

/**
 * 封裝的$_REQUEST超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _request($key, $default = NULL) {
	$_request = filter_input(INPUT_REQUEST, $key);
	return $_request == NULL ? $default : $_request;
}

/**
 * 封裝的$_COOKIE超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _cookie($key, $default = NULL) {
	$_cookie = filter_input(INPUT_COOKIE, $key);
	return $_cookie == NULL ? $default : $_cookie;
}

/**
 * 封裝的$_SESSION超全局變量
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $key
 */
function _session($key, $default = NULL) {
	$_session = filter_input(INPUT_SESSION, $key);
	return $_session == NULL ? $default : $_session;
}

/**
 * 封裝的$_SERVER超全局變量
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $key
 */
function _server($key, $default = NULL) {
	$_server = filter_input(INPUT_SERVER, $key);
	return $_server == NULL ? $default : $_server;
}

/**
 * 封裝的$_FILES超全局變量
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $key
 * @return unknown
 */
function _files($key) {
	return $_FILES[$key];
}
