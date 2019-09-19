<?php

namespace PunchoutCatalog\SecuredWall;

class Client
{
    /** @var array  */
    protected $config = [];
    
    /** @var Client */
    static protected $instance = null;
    
    /**
     * @param array $config
     *
     * @return Client
     * @throws Exception
     */
    static public function getInstance(array $config = [])
    {
        if (null === static::$instance) {
            static::validateConfig($config);
            static::$instance = new Client($config);
        }
        return static::$instance;
    }
    
    /**
     * @param array $config
     *
     * @return bool
     * @throws Exception
     */
    static protected function validateConfig(array $config = [])
    {
        if (empty($config['secret'])) {
            throw new Exception('Empty Secret.', Exception::EMPTY_SECRET);
        }
        if (empty($config['db_host']) && empty($config['db_socket'])) {
            throw new Exception('Empty DB Host and DB Socket.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($config['db_name'])) {
            throw new Exception('Empty DB Name.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($config['db_username'])) {
            throw new Exception('Empty DB Username.', Exception::EMPTY_DB_PARAM);
        }
        if (empty($config['db_password'])) {
            throw new Exception('Empty DB Password.', Exception::EMPTY_DB_PARAM);
        }
        return true;
    }
    
    /**
     * Client constructor.
     *
     * @param array $config
     */
    protected function __construct(array $config = [])
    {
        $this->config = $config;
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
        
        $value = json_encode($value);
        
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
        
        $value = json_decode($value, true);
        
        if ((false === $value) || ('' === $value) || (null === $value)) {
            throw new Exception('Empty Secured Wall Value.', Exception::EMPTY_VALUE_DECODE);
        }
        
        return $value;
    }
    
    /**
     * @param string $id
     * @param string $value
     *
     * @return bool
     */
    protected function write(string $id, string $value)
    {
        return true;
    }
    
    /**
     * @param string $id
     *
     * @return string
     */
    protected function read(string $id)
    {
        return '{"abra" : "abra_value", "test": 99}';
    }
}
