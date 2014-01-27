<?php
/**
 * 數據庫模型對應類
 * 
 * @author hliang
 * @package leaphp
 * @subpackage core
 * @since 1.0.0
 *
 */
class Model {
	protected $id = 'id';
	protected $keys = array();
	
	private $_table = NULL;
	private $_all_keys = array();
	private $_object = NULL;

	/**
	 * 構造函數，初始化一些內部對象
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @throws LeapException
	 */
	public function __construct() {
		if (!$this->id) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '需要为数据模型指定一个主键。'));
		}
		if (!($this->keys)) {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '需要为数据模型指定字段列表。'));
		}
		$this->_table = get_class($this);
		$this->_all_keys = array_merge(array_keys($this->keys), (array)$this->id);
		$this->_object = LeapORM::for_table($this->_table)->use_id_column($this->id);
	}
	
	/**
	 * 取表名
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @throws LeapException
	 */
	public function table() {
		return $this->_table;
	}
	
	/**
	 * 取主键字段名
	 *
	 * @author hliang
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function id_column_name() {
		return leapJoin($this->_table, '.', $this->id);
	}
	
	public function __get($key) {
		if ($key == 'queryString') {
			return LeapORM::get_last_statement()->$key;
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "没有找到对象名 [{$key}]。"));
		}
	}
	
	/**
	 * 取字段名
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return string
	 * @throws LeapException
	 */
	public function __call($key, $params) {
		if (in_array($key, $this->_all_keys)) {
			return leapJoin($this->_table, '.', $key);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, "没有找到字段名 [{$key}]。"));
		}
	}

	/**
	 * 獲取ORM對象，高級操作時使用
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return ORM
	 */
	public function obj() {
		return $this->_object;
	}

	/**
	 * 向數據庫增加一條記錄
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function save(array $data) {
		$_o = $this->_object->create();
		$_o->set($data);
		return $_o->save();
	}

	/**
	 * 從數據庫中刪除一條記錄
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $condition
	 * @throws LeapException
	 * @return boolean
	 */
	public function delete($condition = NULL) {
		$_o = $this->_object;

		if (is_array($condition)) {
			// 传WHERE条件
			foreach ($condition as $key => $block) {
				list($_cond, $value) = explode(':', $block, 2);
				if (in_array($key, $this->_all_keys)) {
					$_where = 'where_' . $_cond;
					$_o = $_o->$_where($key, $value);
				}
			}
			return $_o->delete_many();
		} else if (is_numeric($condition)) {
			// 传主键ID
			$_o = $_o->find_one($condition);
			return $_o->delete();
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '错误的删除条件。'));
		}
	}

	/**
	 * 從數據庫中修改一條記錄
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param array $data
	 * @param string $condition
	 * @throws LeapException
	 * @return boolean
	 */
	public function update(array $data, $condition = NULL) {
		$_o = $this->_object;
		
		if (is_array($condition)) {
			// 传WHERE条件
			foreach ($condition as $key => $block) {
				list($_cond, $value) = explode(':', $block, 2);
				if (in_array($key, $this->_all_keys)) {
					$_where = 'where_' . $_cond;
					$_o = $_o->$_where($key, $value);
				}
			}
			$_o = $_o->find_one();
		} else if (is_numeric($condition)) {
			// 传主键ID
			$_o = $_o->find_one($condition);
		} else {
			throw new LeapException(LeapException::leapMsg(__METHOD__, '错误的查询条件。'));
		}

		$_o->set($data);
		return $_o->save();
	}
	
	/**
	 * 自动在数据库中建表
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param boolean $drop
	 * @return boolean
	 */
	public function create($drop = NULL) {
		// 是否事先删除已经存在的表
		if ($drop) {
			$_sql = "DROP TABLE IF EXISTS `{$this->_table}`;";
		}
		// 生成创建表的SQL语句
		$_sql .= "CREATE TABLE IF NOT EXISTS `{$this->_table}` ( ";
		$_statuement = array("`{$this->id}` int(11) NOT NULL AUTO_INCREMENT");
		foreach ($this->keys as $_key => $_desc) {
			array_push($_statuement, "`{$_key}` {$_desc}");
		}
		// 设置主键
		array_push($_statuement, "PRIMARY KEY (`{$this->id}`)");
		$_sql .= implode(',', $_statuement);
		$_sql .= ');';
		
		// 执行SQL并返回结果
		return LeapORM::get_db()->exec($_sql);
	}
	
	/**
	 * 自动删除数据库中已经存在的表
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public function drop() {
		// 删除表格的SQL语句
		$_sql = "DROP TABLE IF EXISTS `{$this->_table}`;";
		// 执行SQL并返回结果
		return LeapORM::get_db()->exec($_sql);
	}
}
