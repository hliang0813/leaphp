<?php
/**
 * LeapDB数据库操作类，从原生PDO继承来
 * 
 * @author hliang
 * @package leaphp
 * @subpackage libraries
 * @since 1.0.0
 *
 */
class LeapDB extends PDO {
	private $driver;
	private $configure;
	
	private $sql_prepare_cache = NULL;
	private $sth = NULL;
	
	/**
	 * 初始化数据库对象
	 * 参数指定数据库对象连接到配置文件的主（master）或从（slave）服务器
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $cfg_mode
	 */
	public function __construct($cfg_mode = 'master') {
		// 加载配置文件
		$this->driver = LeapConfigure::get('database')['driver'];
		$this->configure = LeapConfigure::get('database')[$cfg_mode];
		// 生成DSN字符串
		$_dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s',
				$this->driver,
				$this->configure['host'],
				$this->configure['port'],
				$this->configure['dbname'],
				$this->configure['charset']);
		// 初始化父类
		parent::__construct($_dsn, $this->configure['username'], $this->configure['password']);
	}
	
	/**
	 * 智能执行SQL语句
	 * 传入第二个参数$bind，则采用预处理的方式来执行
	 * 方法会自动缓存前一次的预处理SQL语句模板，用于下一次处理
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $sql
	 * @param array $bind
	 * @return multitype:|Ambigous <string, number>|Ambigous <unknown, multitype:>
	 */
	public function execute($sql = NULL, $bind = array()) {
		if (!$bind) {
			$_lower_sql = strtolower($sql);
			if (Base::startWith($_lower_sql, 'select')) {
				// SELECT查询
				return $this->executeQuery($sql);
			} else {
				// INSERT、DELETE、UPDATE查询
				return $this->executeExec($sql);
			}
		} else {
			return $this->preExecute($sql, $bind);
		}
	}
	
	/**
	 * 执行SELECT类的查询语句
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $sql
	 * @return multitype:
	 */
	private function executeQuery($sql = NULL) {
		$_result = array();
		// 创建返回的结果集
		foreach (parent::query($sql, PDO::FETCH_ASSOC) as $row) {
			array_push($_result, $row);
		}
		return $_result;
	}
	
	/**
	 * 执行INSERT、DELETE、UPDATE类型的SQL语句
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $sql
	 * @return string|number
	 */
	private function executeExec($sql = NULL) {
		$_lower_sql = strtolower($sql);
		if (Base::startWith($_lower_sql, 'insert')) {
			// INSERT查询
			parent::exec($sql);
			return parent::lastInsertId();
		} else {
			// DELETE、UPDATE查询
			return parent::exec($sql);
		}
	}
	
	/**
	 * 智能执行绑定变量的预处理SQL语句
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $sql
	 * @param array $bind
	 * @throws Exception
	 * @return boolean|multitype:
	 */
	private function preExecute($sql = NULL, array $bind = array()) {
		// 如果没有预处理过这条SQL语句，对其进行预处理，并保证只预处理一次
		if ($this->sql_prepare_cache != $sql) {
			$this->sql_prepare_cache = $sql;
			$this->sth = parent::prepare($sql);
		}
		
		// 如果没有PDOStatuement对象，抛出异常
		if (!$this->sth) {
			throw new Exception('PDOStatuement object not found.');
		}
		
		// 绑定变量
		$_result = $this->sth->execute($bind);
		if (!Base::startWith(strtolower($sql), 'select')) {
			return $_result;
		} else {
			return $this->sth->fetchAll(PDO::FETCH_ASSOC);
		}
	}
}