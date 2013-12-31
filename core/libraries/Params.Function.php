<?php
function _get($key) {
	return filter_input(INPUT_GET, $key);
}

function _post($key) {
	return filter_input(INPUT_POST, $key);
}

function _request($key) {
	return filter_input(INPUT_REQUEST, $key);
}

function _cookie($key) {
	return filter_input(INPUT_COOKIE, $key);
}

function _session($key) {
	return filter_input(INPUT_SESSION, $key);
}

function _server($key) {
	return filter_input(INPUT_SERVER, $key);
}

function _files($key) {
	return $_FILES[$key];
}
