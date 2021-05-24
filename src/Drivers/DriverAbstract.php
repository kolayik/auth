<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Request;
use KolayIK\Auth\Entity\RefreshToken;
use KolayIK\Auth\Logger\AuthLogger;
use KolayIK\Auth\Providers\Storage\StorageInterface;

/**
 * Class DriverAbstract
 *
 * @package KolayIK\Auth\Drivers
 */
abstract class DriverAbstract implements DriverInterface
{
    /** @var AuthLogger */
    protected $logger;

    private $config;

    private $cache;

    protected $ttl = null;

    protected $refreshTtl = null;

    /**
     * DriverAbstract constructor.
     *
     * @param AuthLogger $logger
     */
    public function __construct(AuthLogger $logger)
    {
        $this->logger = $logger;
    }

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
        if (! empty($this->ttl)) {
            return $this->ttl;
        }

        return $this->getConfig()['ttl'];
    }

    /**
     * @return mixed
     */
    public function getRefreshTTL()
    {
        if (! empty($this->refreshTtl)) {
            return $this->refreshTtl;
        }

        return $this->getConfig()['refreshTtl'];
    }

    /**
     * @param $refreshTtl
     */
    public function setRefreshTTL($refreshTtl)
    {
        $this->refreshTtl = $refreshTtl;
    }

    /**
     * @param int $length
     * @return string
     *
     * @throws Exception
     */
    protected function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * @param $userId
     * @return RefreshToken
     *
     * @throws Exception
     */
    public function generateRefreshToken($userId): RefreshToken
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setRefreshToken($this->generateToken());
        $refreshToken->setIpAddress(Request::ip());
        $refreshToken->setUserId($userId);

        $now = Carbon::now();
        $expirationDate = clone $now;
        $refreshToken->setExpirationDate($expirationDate->addMinutes($this->getRefreshTTL()));
        $refreshToken->setCreatedAt($now);
        $refreshToken->setUpdatedAt($now);

        return $this->saveRefreshToken($refreshToken);
    }

    /**
     * @param RefreshToken $data
     * @return RefreshToken
     * @throws Exception
     */
    abstract public function saveRefreshToken(RefreshToken $data): RefreshToken;
}
