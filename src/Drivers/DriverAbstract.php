<?php

namespace KolayIK\Auth\Drivers;

use KolayIK\Auth\Providers\Storage\StorageInterface;

class DriverAbstract
{
    private $config;
    private $cache;

    protected $ttl = null;
    protected $refreshTtl = null;

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param mixed $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $ttl
     */
    public function setTTL($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @return mixed
     */
    public function getTTL()
    {
        if (!empty($this->ttl)) {
            return $this->ttl;
        }
        return $this->getConfig()['ttl'];
    }

    /**
     * @return mixed
     */
    public function getRefreshTTL()
    {
        if (!empty($this->refreshTtl)) {
            return $this->refreshTtl;
        }
        return $this->getConfig()['refreshTtl'];
    }

    /**
     * @param $ttl
     */
    public function setRefreshTTL($refreshTtl)
    {
        $this->refreshTtl = $refreshTtl;
    }

    protected function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
}