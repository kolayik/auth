<?php

namespace KolayIK\Auth\Drivers;

interface DriverInterface
{
    public function get($token);
    public function generate($userId);
    public function invalidate($token);
    public function setConfig($config);
    public function getConfig();
    public function getCache();
    public function setCache($cache);
    public function getTTL();
    public function setTTL($ttl);
}