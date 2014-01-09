<?php
/**
 * Redis緩存操作類
 * 
 * @author hliang
 * @package leaphp
 * @subpackage sysplugins
 * @since 1.0.0
 *
 */
class RedisClient {
	static private $configure = array();
	
	static private $conn_master;
	static private $conn_slave;
	
	static private $method_read = array (
		'connect',
		'pconnect',
		'close',
		'ping',
		'echo',
		'get',
		'randomKey',
		'renameKey',
		'renameNx',
		'getMultiple',
		'exists',
		'type',
		'getRange',
		'getBit',
		'strlen',
		'getKeys',
		'sort',
		'sortAsc',
		'sortAscAlpha',
		'sortDesc',
		'sortDescAlpha',
		'lSize',
		'listTrim',
		'lGet',
		'lGetRange',
		'lInsert',
		'sSize',
		'sRandMember',
		'sContains',
		'sMembers',
		'sInter',
		'sInterStore',
		'sUnion',
		'sUnionStore',
		'sDiff',
		'sDiffStore',
		'dbSize',
		'auth',
		'ttl',
		'pttl',
		'persist',
		'info',
		'select',
		'move',
		'bgrewriteaof',
		'slaveof',
		'object',
		'bitop',
		'bitcount',
		'zRange',
		'zReverseRange',
		'zRangeByScore',
		'zRevRangeByScore',
		'zCount',
		'zCard',
		'zScore',
		'zRank',
		'zRevRank',
		'zInter',
		'zUnion',
		'expireAt',
		'pexpire',
		'pexpireAt',
		'hGet',
		'hLen',
		'hKeys',
		'hVals',
		'hGetAll',
		'hExists',
		'hMget',
		'multi',
		'discard',
		'exec',
		'pipeline',
		'watch',
		'unwatch',
		'publish',
		'subscribe',
		'psubscribe',
		'unsubscribe',
		'punsubscribe',
		'time',
		'eval',
		'evalsha',
		'script',
		'dump',
		'restore',
		'migrate',
		'getLastError',
		'clearLastError',
		'client',
		'getOption',
		'config',
		'slowlog',
		'getHost',
		'getPort',
		'getDBNum',
		'getTimeout',
		'getReadTimeout',
		'getPersistentID',
		'getAuth',
		'isConnected',
		'open',
		'popen',
		'lLen',
		'sGetMembers',
		'mget',
		'expire',
		'zunionstore',
		'zinterstore',
		'zSize',
		'substr',
		'rename',
		'keys',
		'ltrim',
		'lindex',
		'lrange',
		'scard',
		'sismember',
		'zrevrange',
		'sendEcho',
		'evaluate',
		'evaluateSha',
	);
	
	/**
	 * 構造函數，加載配置文件內容
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param string $config
	 * @throws LeapException
	 */
	public function __construct($config = 'redis') {
		$_class_redis_exists = class_exists('Redis');
		if (!$_class_redis_exists) {
			throw new LeapException(LeapException::leapMsg('lpf_plugins::' . __METHOD__, '没有检测到PHP-REDIS扩展。'));
		}
		
		$logger = LeapLogger::getLogger('lpf_plugins::' . __METHOD__);
		$logger->trace(leapJoin('RedisClient配置文件 -> ', $config));
		
		self::$configure = LeapConfigure::get($config);
	}
	
	/**
	 * 切換主從服務器的連接
	 * 
	 * @author hliang
	 * @since 1.0.0
	 * 
	 * @param unknown $method
	 * @return Redis
	 */
	private function changeConnection($method) {
		$logger = LeapLogger::getLogger('lpf_plugins::' . __METHOD__);
		$logger->trace(leapJoin('选择主从连接。方法 -> ', $method));
		
		if (!in_array($method, self::$method_read)) {
			$logger->trace('判定请求方法为写方法，选择主连接。');
			$used_config = array(
				'host' => self::$configure['master']['host'],
				'port' => self::$configure['master']['port'],
			);
			
			if (!$this->conn_master) {
				$logger->trace('初始化Redis主链接对象。连接 -> ' . var_export($used_config, true));
				$this->conn_master = new Redis();
				$this->conn_master->pconnect($used_config['host'], $used_config['port']);
			}
			
			return $this->conn_master;
		} else {
			$logger->trace('判定请求方法为读方法，选择从连接。');
			$pool = array(self::$configure['master']);
			if (array_key_exists('slave', self::$configure)) {
				$logger->trace('从连接配置为单连接。');
				array_push($pool, self::$configure['slave']);
			} elseif (array_key_exists('slaves', self::$configure)) {
				$logger->trace('从连接配置为多连接。');
				foreach (self::$configure['slaves'] as $config) {
					array_push($pool, $config);
				}
			}
			
			$used_config = $pool[rand(0, count($pool) - 1)];
			if (!$this->conn_slave) {
				$logger->trace('初始化Redis从连接对象。连接 -> ' . var_export($used_config, true));
				$this->conn_slave = new Redis();
				$this->conn_slave->pconnect($used_config['host'], $used_config['port']);
			}
			
			return $this->conn_slave;
		}		
	}
	
	/**
	 * 魔術方法，自動調用php-redis類的對應方法
	 * 
	 * @author hliang
	 * @since 1.0.0 
	 * 
	 * @param unknown $name
	 * @param unknown $arguments
	 */
	public function __call($name, $arguments) {
		$conn = self::changeConnection($name);
		return call_user_func_array(array($conn, $name), $arguments);
	}
}