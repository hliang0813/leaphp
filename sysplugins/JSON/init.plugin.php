<?php
class JSON {
	static private $tips_error = 'JSON解码错误：';

	static public function encode($value, $options = 0) {
		return json_encode($value, $options);
	}

	static public function decode($json, $assoc = false, $depth = 512, $options = 0) {
		$string = json_decode($json, $assoc, $depth, $options);
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				return Base::response($string, NULL);
				break;
			case JSON_ERROR_DEPTH:
				return Base::response(self::$tips_error . '到达了最大堆栈深度', TRUE);
				break;
			case JSON_ERROR_STATE_MISMATCH:
				return Base::response(self::$tips_error . '无效或异常的JSON', TRUE);
				break;
			case JSON_ERROR_CTRL_CHAR:
				return Base::response(self::$tips_error . '控制字符错误，可能是编码不对', TRUE);
				break;
			case JSON_ERROR_SYNTAX:
				return Base::response(self::$tips_error . '语法错误', TRUE);
				break;
			case JSON_ERROR_UTF8:
				return Base::response(self::$tips_error . '异常的UTF-8字符，也许是因为不正确的编码', TRUE);
				break;
			default:
				return Base::response(self::$tips_error . '未知错误', TRUE);
				break;
		}
	}
}
