<?php

namespace PunchoutCatalog\SecuredWall;

class Client
{
    static protected $config = [];
    
    /** @var Client */
    static protected $instance = null;
    
    static public function getInstance(array $config = [])
    {
        if (null === static::$instance) {
            static::$instance = new Client($config);
        }
        return static::$instance;
    }
    
    protected function __construct(array $config = [])
    {
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
