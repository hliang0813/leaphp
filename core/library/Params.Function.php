<?php
function _get($key) {
	return $_GET[$key];
}

function _post($key) {
	return $_POST[$key];
}

function _request($key) {
	return $_REQUEST[$key];
}

function _cookie($key) {
	return $_COOKIE[$key];
}

function _session($key) {
	return $_SESSION[$key];
}

function _files($key) {
	return $_FILES[$key];
}
