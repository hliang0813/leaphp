<?php
leapCheckEnv();
/**
 * 文件上传操作
 * 
 * @author hliang
 * @package leaphp
 * @subpackage sysplugins
 * @since 1.0.0
 *
 */
class Upload {
	private $configure = array();
	private $_config;
	private $_field;
	private $_upfile_info;
	private $key_save_path = 'save_path';
	private $key_visit_path = 'visit_path';
	
	private $_limit = array();
	
	private $_convert = 1024;
	
	
	public function __construct($key = NULL) {
		$key = !isset($key) ? 'upload' : $key;
		$this->configure = LeapConfigure::get($key);
		if (!array_key_exists($this->key_save_path, $this->configure) || !array_key_exists($this->key_visit_path, $this->configure)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "上传文件插件的配置文件错误 [{$key}]"));
		}
	}

	/**
	 * 设置文件上传限制
	 * $limit['extension'] 允许上传文件的扩展名，多个文件名用英文逗号分隔
	 * $limit['maxsize'] 允许上传的文件大小，以KB为单位
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param array $limit
	 */
	public function limit($limit = array()) {
		if (!is_array($limit)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '上传文件的限制参数不是数组。'));
		}
		$this->_limit = $limit;
		
		return Base::response('');
	}

	/**
	 * 上传文件动作
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $field_name
	 * @param string $sub_folder
	 * @param string $rename
	 * @return multitype:number string 
	 */
	public function send($field_name = NULL, $sub_folder = '', $rename = '') {
		if (isset($field_name)) {
			$this->_field = $_FILES[$field_name];
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '请指定上传文件域的名称。'));
		}
		
		if (!array_key_exists($field_name, $_FILES)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "未找到指定的文件域名称 [{$field_name}]。"));
		}
		
		$this->_limit = $this->_finalLimit()->result;
		$this->_upfile_info = (object)array_merge($this->_field, pathinfo($this->_field['name']));
		
		// 处理上传错误
		$_up_error = $this->handleUploadError();
		if ($_up_error !== true) {
			return Base::response($_up_error, '001');
		}
		
		// 判断上传文件扩展名及mimetype
		$_type_error = $this->handleFileExtensionMimeValidate();
		if ($_type_error !== true) {
			return Base::response($_type_error, '002');
		}
		
		// 判断上传文件尺寸
		$_size_error = $this->handleFileMaxSizeValidate();
		if ($_size_error !== true) {
			return Base::response($_size_error, '003');
		}
				
		if (is_uploaded_file($this->_upfile_info->tmp_name)) {
			$_dst_dir = trim($sub_folder) == '' ? $this->configure[$this->key_save_path] : leapJoin($this->configure[$this->key_save_path], DS, $sub_folder);
			if (!file_exists($_dst_dir)) {
				LeapFunction('mkdirs', $_dst_dir);
			}
			
			$_dst_filename = trim($rename) == '' ? uniqid() : $rename;
			$_dst_full = leapJoin($_dst_dir, DS, $_dst_filename, '.', $this->_upfile_info->extension);
			if (move_uploaded_file($this->_upfile_info->tmp_name, $_dst_full)) {
				
				$_pathinfo = pathinfo($_dst_full);
				$_uploaded = array(
					'realpath' => realpath($_dst_full),
					'url' => leapJoin($this->configure[$this->key_visit_path], '/', $sub_folder, '/', $_pathinfo['basename']),
					'size' => ceil($this->_upfile_info->size / $this->_convert),
				);
				return Base::response((object)$_uploaded);
			} else {
				return Base::response('服务器转移文件时发生异常。', '004');
			}
		} else {
			return Base::response('服务器保存文件时发生异常。', '005');
		}
	}
	
	private function _finalLimit() {
		$default = array(
			'extension' => '',
			'maxsize' => 0,
		);
		
		$_limit = array_merge($default, $this->_limit);
		
		$_extension = str_replace(' ', '', $_limit['extension']);
		
		$_limit['extension'] = strlen($_extension) == 0 ? array() : explode(',', strtolower($_extension));
		$_limit['maxsize'] = intval($_limit['maxsize']);
		
		return Base::response((object)$_limit);
	}

	/**
	 * 处理上传错误方法
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return string|boolean
	 */
	private function handleUploadError() {
		switch ($this->_upfile_info->error) {
			case "1":
				return "文件大小超过服务器限制";
				break;
			case "2":
				return "文件大小超过限制";
				break;
			case "3":
				return "文件上传不完整";
				break;
			case "4":
				return "请选择一个文件进行上传操作";
				break;
		}
		return true;
	}
	
	private function handleFileExtensionMimeValidate() {
		$_extension = strtolower($this->_upfile_info->extension);
		if (!in_array($_extension, $this->_limit->extension)) {
			return "上传文件的扩展名不正确 [{$_extension}]。";
		}
		
		if (!in_array($this->_upfile_info->type, $this->checkMimeType($this->_limit->extension))) {
			return "上传文件的MimeType与其扩展名不对应 [{$this->_upfile_info->type}]。";
		}
		
		return true;
	}
	
	private function handleFileMaxSizeValidate() {
		if ($this->_limit->maxsize < ($this->_upfile_info->size / $this->_convert)) {
			return "上传文件的大小超过了服务器限制 [{$this->_limit->maxsize}KB]。";
		}
		
		return true;
	}


	/**
	 * 检查文件的MIMETYPE
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $extensions
	 * @return multitype:string 
	 */
	private function checkMimeType($extensions) {
		$mimetype = array(
			'323'   => array('text/h323'),
			'7z'    => array('application/x-7z-compressed'),
			'abw'   => array('application/x-abiword'),
			'acx'   => array('application/internet-property-stream'),
			'ai'    => array('application/postscript'),
			'aif'   => array('audio/x-aiff'),
			'aifc'  => array('audio/x-aiff'),
			'aiff'  => array('audio/x-aiff'),
			'amf'   => array('application/x-amf'),
			'asf'   => array('video/x-ms-asf'),
			'asr'   => array('video/x-ms-asf'),
			'asx'   => array('video/x-ms-asf'),
			'atom'  => array('application/atom+xml'),
			'avi'   => array('video/avi', 'video/msvideo', 'video/x-msvideo'),
			'bin'   => array('application/octet-stream','application/macbinary'),
			'bmp'   => array('image/bmp'),
			'c'     => array('text/x-csrc'),
			'c++'   => array('text/x-c++src'),
			'cab'   => array('application/x-cab'),
			'cc'    => array('text/x-c++src'),
			'cda'   => array('application/x-cdf'),
			'class' => array('application/octet-stream'),
			'cpp'   => array('text/x-c++src'),
			'cpt'   => array('application/mac-compactpro'),
			'csh'   => array('text/x-csh'),
			'css'   => array('text/css'),
			'csv'   => array('text/x-comma-separated-values', 'application/vnd.ms-excel', 'text/comma-separated-values', 'text/csv'),
			'dbk'   => array('application/docbook+xml'),
			'dcr'   => array('application/x-director'),
			'deb'   => array('application/x-debian-package'),
			'diff'  => array('text/x-diff'),
			'dir'   => array('application/x-director'),
			'divx'  => array('video/divx'),
			'dll'   => array('application/octet-stream', 'application/x-msdos-program'),
			'dmg'   => array('application/x-apple-diskimage'),
			'dms'   => array('application/octet-stream'),
			'doc'   => array('application/msword'),
			'docx'  => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
			'dvi'   => array('application/x-dvi'),
			'dxr'   => array('application/x-director'),
			'eml'   => array('message/rfc822'),
			'eps'   => array('application/postscript'),
			'evy'   => array('application/envoy'),
			'exe'   => array('application/x-msdos-program', 'application/octet-stream'),
			'fla'   => array('application/octet-stream'),
			'flac'  => array('application/x-flac'),
			'flc'   => array('video/flc'),
			'fli'   => array('video/fli'),
			'flv'   => array('video/x-flv'),
			'gif'   => array('image/gif'),
			'gtar'  => array('application/x-gtar'),
			'gz'    => array('application/x-gzip'),
			'h'     => array('text/x-chdr'),
			'h++'   => array('text/x-c++hdr'),
			'hh'    => array('text/x-c++hdr'),
			'hpp'   => array('text/x-c++hdr'),
			'hqx'   => array('application/mac-binhex40'),
			'hs'    => array('text/x-haskell'),
			'htm'   => array('text/html'),
			'html'  => array('text/html'),
			'ico'   => array('image/x-icon'),
			'ics'   => array('text/calendar'),
			'iii'   => array('application/x-iphone'),
			'ins'   => array('application/x-internet-signup'),
			'iso'   => array('application/x-iso9660-image'),
			'isp'   => array('application/x-internet-signup'),
			'jar'   => array('application/java-archive'),
			'java'  => array('application/x-java-applet'),
			'jpe'   => array('image/jpeg', 'image/pjpeg'),
			'jpeg'  => array('image/jpeg', 'image/pjpeg'),
			'jpg'   => array('image/jpeg', 'image/pjpeg'),
			'js'    => array('application/x-javascript'),
			'json'  => array('application/json'),
			'latex' => array('application/x-latex'),
			'lha'   => array('application/octet-stream'),
			'log'   => array('text/plain', 'text/x-log'),
			'lzh'   => array('application/octet-stream'),
			'm4a'   => array('audio/mpeg'),
			'm4p'   => array('video/mp4v-es'),
			'm4v'   => array('video/mp4'),
			'man'   => array('application/x-troff-man'),
			'mdb'   => array('application/x-msaccess'),
			'midi'  => array('audio/midi'),
			'mid'   => array('audio/midi'),
			'mif'   => array('application/vnd.mif'),
			'mka'   => array('audio/x-matroska'),
			'mkv'   => array('video/x-matroska'),
			'mov'   => array('video/quicktime'),
			'movie' => array('video/x-sgi-movie'),
			'mp2'   => array('audio/mpeg'),
			'mp3'   => array('audio/mpeg'),
			'mp4'   => array('application/mp4','audio/mp4','video/mp4'),
			'mpa'   => array('video/mpeg'),
			'mpe'   => array('video/mpeg'),
			'mpeg'  => array('video/mpeg'),
			'mpg'   => array('video/mpeg'),
			'mpg4'  => array('video/mp4'),
			'mpga'  => array('audio/mpeg'),
			'mpp'   => array('application/vnd.ms-project'),
			'mpv'   => array('video/x-matroska'),
			'mpv2'  => array('video/mpeg'),
			'ms'    => array('application/x-troff-ms'),
			'msg'   => array('application/msoutlook','application/x-msg'),
			'msi'   => array('application/x-msi'),
			'nws'   => array('message/rfc822'),
			'oda'   => array('application/oda'),
			'odb'   => array('application/vnd.oasis.opendocument.database'),
			'odc'   => array('application/vnd.oasis.opendocument.chart'),
			'odf'   => array('application/vnd.oasis.opendocument.forumla'),
			'odg'   => array('application/vnd.oasis.opendocument.graphics'),
			'odi'   => array('application/vnd.oasis.opendocument.image'),
			'odm'   => array('application/vnd.oasis.opendocument.text-master'),
			'odp'   => array('application/vnd.oasis.opendocument.presentation'),
			'ods'   => array('application/vnd.oasis.opendocument.spreadsheet'),
			'odt'   => array('application/vnd.oasis.opendocument.text'),
			'oga'   => array('audio/ogg'),
			'ogg'   => array('application/ogg'),
			'ogv'   => array('video/ogg'),
			'otg'   => array('application/vnd.oasis.opendocument.graphics-template'),
			'oth'   => array('application/vnd.oasis.opendocument.web'),
			'otp'   => array('application/vnd.oasis.opendocument.presentation-template'),
			'ots'   => array('application/vnd.oasis.opendocument.spreadsheet-template'),
			'ott'   => array('application/vnd.oasis.opendocument.template'),
			'p'     => array('text/x-pascal'),
			'pas'   => array('text/x-pascal'),
			'patch' => array('text/x-diff'),
			'pbm'   => array('image/x-portable-bitmap'),
			'pdf'   => array('application/pdf', 'application/x-download'),
			'php'   => array('application/x-httpd-php'),
			'php3'  => array('application/x-httpd-php'),
			'php4'  => array('application/x-httpd-php'),
			'php5'  => array('application/x-httpd-php'),
			'phps'  => array('application/x-httpd-php-source'),
			'phtml' => array('application/x-httpd-php'),
			'pl'    => array('text/x-perl'),
			'pm'    => array('text/x-perl'),
			'png'   => array('image/png', 'image/x-png'),
			'po'    => array('text/x-gettext-translation'),
			'pot'   => array('application/vnd.ms-powerpoint'),
			'pps'   => array('application/vnd.ms-powerpoint'),
			'ppt'   => array('application/powerpoint'),
			'pptx'  => array('application/vnd.openxmlformats-officedocument.presentationml.presentation'),
			'ps'    => array('application/postscript'),
			'psd'   => array('application/x-photoshop', 'image/x-photoshop'),
			'pub'   => array('application/x-mspublisher'),
			'py'    => array('text/x-python'),
			'qt'    => array('video/quicktime'),
			'ra'    => array('audio/x-realaudio'),
			'ram'   => array('audio/x-realaudio', 'audio/x-pn-realaudio'),
			'rar'   => array('application/rar'),
			'rgb'   => array('image/x-rgb'),
			'rm'    => array('audio/x-pn-realaudio'),
			'rpm'   => array('audio/x-pn-realaudio-plugin', 'application/x-redhat-package-manager'),
			'rss'   => array('application/rss+xml'),
			'rtf'   => array('text/rtf'),
			'rtx'   => array('text/richtext'),
			'rv'    => array('video/vnd.rn-realvideo'),
			'sea'   => array('application/octet-stream'),
			'sh'    => array('text/x-sh'),
			'shtml' => array('text/html'),
			'sit'   => array('application/x-stuffit'),
			'smi'   => array('application/smil'),
			'smil'  => array('application/smil'),
			'so'    => array('application/octet-stream'),
			'src'   => array('application/x-wais-source'),
			'svg'   => array('image/svg+xml'),
			'swf'   => array('application/x-shockwave-flash'),
			't'     => array('application/x-troff'),
			'tar'   => array('application/x-tar'),
			'tcl'   => array('text/x-tcl'),
			'tex'   => array('application/x-tex'),
			'text'  => array('text/plain'),
			'texti' => array('application/x-texinfo'),
			'textinfo' => array('application/x-texinfo'),
			'tgz'   => array('application/x-tar'),
			'tif'   => array('image/tiff'),
			'tiff'  => array('image/tiff'),
			'torrent' => array('application/x-bittorrent'),
			'tr'    => array('application/x-troff'),
			'tsv'   => array('text/tab-separated-values'),
			'txt'   => array('text/plain'),
			'wav'   => array('audio/x-wav'),
			'wax'   => array('audio/x-ms-wax'),
			'wbxml' => array('application/wbxml'),
			'wm'    => array('video/x-ms-wm'),
			'wma'   => array('audio/x-ms-wma'),
			'wmd'   => array('application/x-ms-wmd'),
			'wmlc'  => array('application/wmlc'),
			'wmv'   => array('video/x-ms-wmv', 'application/octet-stream'),
			'wmx'   => array('video/x-ms-wmx'),
			'wmz'   => array('application/x-ms-wmz'),
			'word'  => array('application/msword', 'application/octet-stream'),
			'wp5'   => array('application/wordperfect5.1'),
			'wpd'   => array('application/vnd.wordperfect'),
			'wvx'   => array('video/x-ms-wvx'),
			'xbm'   => array('image/x-xbitmap'),
			'xcf'   => array('image/xcf'),
			'xhtml' => array('application/xhtml+xml'),
			'xht'   => array('application/xhtml+xml'),
			'xl'    => array('application/excel', 'application/vnd.ms-excel'),
			'xla'   => array('application/excel', 'application/vnd.ms-excel'),
			'xlc'   => array('application/excel', 'application/vnd.ms-excel'),
			'xlm'   => array('application/excel', 'application/vnd.ms-excel'),
			'xls'   => array('application/excel', 'application/vnd.ms-excel'),
			'xlsx'  => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
			'xlt'   => array('application/excel', 'application/vnd.ms-excel'),
			'xml'   => array('text/xml', 'application/xml'),
			'xof'   => array('x-world/x-vrml'),
			'xpm'   => array('image/x-xpixmap'),
			'xsl'   => array('text/xml'),
			'xvid'  => array('video/x-xvid'),
			'xwd'   => array('image/x-xwindowdump'),
			'z'     => array('application/x-compress'),
			'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed')
		);
		foreach ($extensions as $value) {
			if (is_array($mimetype[trim($value)])) {
				foreach ($mimetype[trim($value)] as $mimekey => $mimevalue) {
					$return[] = $mimevalue;
				}
			}
		}
		return $return;
	}

}
