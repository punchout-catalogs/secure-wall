<?php

namespace PunchoutCatalog\SecuredWall;

use Illuminate\Database\Capsule\Manager as ManagerDB;

/**
 * Class Client
 *
 * @package PunchoutCatalog\SecuredWall
 */
class Client
{
    protected $table = 'pgw_secured_wall';
    
    /** @var string */
    protected $secret;
    
    /** @var array  */
    protected $dbConfig = [
        'driver'    => 'mysql',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ];
    
    /** @var Client */
    static protected $instance = null;
    
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;
    
    /** @var \Illuminate\Encryption\Encrypter */
    protected $encryter;
    
    /**
     * @param string $secret
     * @param array $dbConfig
     *
     * @return Client
     * @throws Exception
     */
    static public function getInstance(string $secret, array $dbConfig = [])
    {
        if (null === static::$instance) {
            static::validate($secret, $dbConfig);
            static::$instance = new Client($secret, $dbConfig);
        }
        return static::$instance;
    }
    
    /**
     * @param string $secret
     * @param array $dbConfig
     *
     * @return bool
     * @throws Exception
     */
    static protected function validate(string $secret, array $dbConfig = [])
    {
        if (empty($secret)) {
            throw new Exception('Empty Secret.', Exception::EMPTY_SECRET);
        }
        if (empty($dbConfig)) {
            throw new Exception('Empty DB Connection.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($dbConfig['host']) && empty($config['unix_socket'])) {
            throw new Exception('Empty DB Host and DB Socket.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($dbConfig['database'])) {
            throw new Exception('Empty DB Name.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($dbConfig['username'])) {
            throw new Exception('Empty DB Username.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($dbConfig['password'])) {
            throw new Exception('Empty DB Password.', Exception::EMPTY_DB_PARAM);
        }
        return true;
    }
    
    /**
     * @param string $secret
     * @param array $dbConfig
     *
     * @param array $config
     */
    protected function __construct(string $secret, array $dbConfig = [])
    {
        $this->secret = $secret;
        $this->encryter = new \Illuminate\Encryption\Encrypter($secret, 'AES-256-CBC');
        $this->dbConfig = array_merge($this->dbConfig, $dbConfig);
        $this->initDb();
    }
    
    /**
     * @return $this
     */
    protected function initDb()
    {
        $this->db = new ManagerDB();
        $this->db->addConnection($this->dbConfig, 'poc_secure_wall');
        return $this;
    }
    
    /**
     * @param string $id
     * @param array|string $value
     *
     * @return bool
     * @throws Exception
     */
    public function set(string $id, $value)
    {
        if ('' === $id) {
            throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
        }
        if ('' === $value) {
            throw new Exception('Empty Secured Wall Value.', Exception::EMPTY_VALUE_ENCODE);
        }
        
        $value = $this->encode($value);
        
        return $this->write($id, $value);
    }
    
    /**
     * @param string $id
     *
     * @return array|string
     * @throws Exception
     */
    public function get(string $id)
    {
        if ('' === $id) {
            throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
        }
        
        $value = $this->read($id);
        if ((false === $value) || ('' === $value) || (null === $value)) {
            throw new Exception('Requested Key does not exists.', Exception::EMPTY_VALUE);
        }
        
        $value = $this->decode($value);
        if ((false === $value) || ('' === $value) || (null === $value)) {
            throw new Exception('Empty Secured Wall Value.', Exception::EMPTY_VALUE_DECODE);
        }
        
        return $value;
    }
    
    /**
     * @param string $id
     *
     * @return array|string
     * @throws Exception
     */
    public function exists(string $id)
    {
        if ('' === $id) {
            throw new Exception('Empty Secured Wall ID.', Exception::EMPTY_ID);
        }
        
        $value = $this->read($id);
        if ((false === $value) || ('' === $value) || (null === $value)) {
            return false;
        }
        
        return true;
    }

    /**
     * @param string $id
     * @param string $value
     *
     * @return bool
     */
    protected function write(string $id, string $value)
    {
        $id = $this->toId($id);
        
        $sql = sprintf("INSERT INTO `%s` (`id`, `value`)", $this->table)
        . " VALUES (:id, :value)"
        . " ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)";
        
        return $this->getConnection()->insert($sql, ['id' => $id, 'value' => $value]);
    }
    
    /**
     * @param string $id
     *
     * @return string
     */
    protected function read(string $id)
    {
        $id = $this->toId($id);
        
        /** @var \Illuminate\Support\Collection $result */
        $result = $this->getConnection()->Table($this->table)
            ->select('value')
            ->where('id', $id)
            ->limit(1)
            ->pluck('value');
        
        $result = $result->first();

        return (null !== $result) ? $result : null;
    }
    
    protected function toId(string $id)
    {
        return md5(trim($id));
    }
    
    protected function getConnection()
    {
        return $this->db->getConnection('poc_secure_wall');
    }
    
    protected function encode($value)
    {
        $value = json_encode($value);
        return $this->encryter->encrypt($value, false);
    }
    
    protected function decode($value)
    {
        $value = $this->encryter->decrypt($value, false);
        return json_decode($value, true);
    }
}
