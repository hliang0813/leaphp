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
function _get($key) {
	return filter_input(INPUT_GET, $key);
}

/**
 * 封裝的$_POST超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _post($key) {
	return filter_input(INPUT_POST, $key);
}

/**
 * 封裝的$_REQUEST超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _request($key) {
	return filter_input(INPUT_REQUEST, $key);
}

/**
 * 封裝的$_COOKIE超全局變量
 * 
 * @author hliang
 * @since 1.0.0
 * 
 * @param unknown $key
 */
function _cookie($key) {
	return filter_input(INPUT_COOKIE, $key);
}

/**
 * 封裝的$_SESSION超全局變量
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $key
 */
function _session($key) {
	return filter_input(INPUT_SESSION, $key);
}

/**
 * 封裝的$_SERVER超全局變量
 * 
 * @author hliang
 * @since 1.0.0 
 * 
 * @param unknown $key
 */
function _server($key) {
	return filter_input(INPUT_SERVER, $key);
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
