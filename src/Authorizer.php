<?php

namespace KolayIK\Auth;

use KolayIK\Auth\Drivers\DriverInterface;
use KolayIK\Auth\Entity\AuthToken;
use KolayIK\Auth\Http\Parser\Parser;

class Authorizer
{
    public $config;
    public $driver;
    public $parser;

    public $token = null;
    public $refreshToken = null;

    public function __construct($config, Parser $parser, DriverInterface $driver)
    {
        $this->config = $config;
        $this->parser = $parser;
        $this->driver = $driver;
    }

    /**
     * @param $userId
     * @return AuthToken
     */
    public function generate($userId)
    {
        return [
            "authToken" => $this->driver->generate($userId),
            "refreshToken" => $this->driver->generateRefreshToken($userId)->getRefreshToken()
        ];
    }

    /**
     * @return string
     */
    public function getToken()
    {
        $this->token = $this->parser->parseToken();
        return $this->token;
    }

    /**
     * @return bool
     */
    public function invalidate()
    {
        $token = $this->parser->parseToken();
        return $this->driver->invalidate($token);
    }

    /**
     * @return AuthToken
     */
    public function authenticate()
    {
        $token = $this->getToken();
        $tokenObject = $this->driver->get($token);
        return $tokenObject;
    }

    /**
     * Refresh the token
     */
    public function refresh()
    {
        $clientRefreshToken = $this->getToken();
        return $this->driver->refresh($clientRefreshToken);
    }

    /**
     * @param $ttl
     * @return $this
     */
    public function setTTL($ttl)
    {
        $this->driver->setTTL($ttl);
        return $this;
    }
}