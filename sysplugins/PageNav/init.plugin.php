<?php
leapCheckEnv();
/**
 * 数据库分页插件
 * 需要配合LeapDB数据库类使用
 * 
 * 例：
 * $obj = new PageNav(new LeapDB());
 * $obj->sql('select * from table limit 10');
 * // $obj->setLookup($lookup = 5);
 * $pageinfo = $obj->exec($page = 1);
 * 
 * @author hliang
 * @package leaphp
 * @subpackage sysplugins
 * @since 1.0.0
 *
 */
class PageNav extends Base {
	// 数据库对象
	private $db;
	// 查询的SQL语句
	private $sql_original = '';
	private $sql_query = '';
	private $sql_count = '';
	
	// 分页需要的数字元素
	private $_current_page = 0;
	private $_start_record = 0;
	private $_limit_record = 10;
	private $_pagelist_lookup = 5;
	
	// 分页数据信息
	private $_total_record = 0;
	private $_total_page = 0;
	
	/**
	 * 构造函数，获取一个LeapDB的数据库操作对象
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param LeapDB $db
	 * @throws LeapException
	 */
	public function __construct($db) {
		if (!($db instanceof LeapDB)) {
			throw new LeapException(leapException::leapMsg(__METHOD__, 'LeapDB object not found.'));
		}
		$this->db = $db;
	}
	
	/**
	 * 接收查询第一页数据的SQL语句，并对其进行初步处理
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $sql
	 * @throws LeapException
	 * @return PageNav
	 */
	public function sql($sql) {
		preg_match('/(?P<limit_substring>\s+limit\s+(?P<limit_record>\d+)$)/i', $sql, $matches);		
		if (!array_key_exists('limit_substring', $matches) || !array_key_exists('limit_record', $matches)) {
			throw new LeapException(leapException::leapMsg(__METHOD__, 'Substatuement "LIMIT xx" not found.'));
		}
		
		$this->sql_original = str_replace($matches['limit_substring'], '', $sql);
		$this->_limit_record = intval($matches['limit_record']);
		
		if ($this->_limit_record <= 0) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Limit number in substatuement "LIMIT xx" must be a POSITIVE NUMBER.'));
		}
		
		return $this;
	}
	
	public function setLookup($lookup = 5) {
		$lookup = intval($lookup);
		if ($lookup <= 0) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Lookup number must be a POSITIVE NUMBER'));
		}
		
		$this->_pagelist_lookup = $lookup;
	}
	
	/**
	 * 执行分页查询操作，进行再次数据库查询
	 * 第一次，查询需要页面的数据集合
	 * 第二次，查询全部记录总数量
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param number $page
	 * @throws LeapException
	 * @return Array
	 */
	public function exec($page = 1) {
		$page = intval($page) == 0 ? 1 : intval($page);
		if ($page <= 0) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, 'Requested page number must be a POSITIVE NUMBER.'));
		}
		
		$this->_current_page = $page;
		$this->_start_record = ($page - 1) * $this->_limit_record;
		
		$this->_total_record = $this->queryCount();
		$this->_total_page = ceil($this->_total_record/$this->_limit_record);
		$_data_set = ($this->_current_page > $this->_total_page) ? array() : $this->queryDataList();
		
		$_page_info = array(
			'data' => $_data_set,
			'info' => array(
				'total_record' => $this->_total_record,
				'total_page' => $this->_total_page,
				'current_page' => $page,
				'page_list' => $this->getPageList(),
			),
		);
		
		return $_page_info;
	}
	
	/**
	 * 获取页码列表
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return multitype:
	 */
	private function getPageList() {
		
		$_pagelist_ary = array();
		
		if ($this->_current_page > $this->_total_page) {
			return $_pagelist_ary;
		}

		for ($i = $this->_current_page - $this->_pagelist_lookup; $i <= $this->_current_page + $this->_pagelist_lookup; $i ++) {
			if ($i <= 0) {
				continue;
			}
			array_push($_pagelist_ary, $i);
			if ($i >= $this->_total_page) {
				break;
			}
		}
		
		if ($_pagelist_ary[0] > 1) {
			array_unshift($_pagelist_ary, '...');
		}
		
		if ($_pagelist_ary[count($_pagelist_ary) - 1] < $this->_total_page) {
			array_push($_pagelist_ary, '...');
		}
		
		return $_pagelist_ary;
	}
	
	/**
	 * 生成查询结果集的SQL语句
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	private function generateQuerySQL() {
		$this->sql_query = sprintf('%s LIMIT %d, %d', $this->sql_original, $this->_start_record, $this->_limit_record);
		return $this->sql_query;
	}
	
	/**
	 * 生成查询总记录数的SQL语句
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	private function generateCountSQL() {
		$this->sql_count = preg_replace('/^select\s+(.+)\s+from\s+/i', 'SELECT COUNT(1) AS total FROM ', $this->sql_original);
		return $this->sql_count;
	}
	
	/**
	 * 查询当前页面数据结果集
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return Array
	 */
	private function queryDataList() {
		$_sql = $this->generateQuerySQL();
		return $this->db->execute($_sql);
	}
	
	/**
	 * 查询总记录数量
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 */
	private function queryCount() {
		$_sql = $this->generateCountSQL();
		$_total = $this->db->execute($_sql);
		return $_total[0]['total'];
	}
}

