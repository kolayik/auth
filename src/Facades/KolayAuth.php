<?php

namespace KolayIK\Auth\Facades;

use KolayIK\Auth\Entity\AuthToken;

class KolayAuth
{
    /**
     * @param $ttl
     * @return $this
     */
    public static function setTTL($ttl)
    {
        return \KolayIK\Auth\Facades\Authorizer::setTTL($ttl);
    }

    /**
     * @param $userId
     * @return array
     */
    public static function generate($userId)
    {
        return \KolayIK\Auth\Facades\Authorizer::generate($userId);
    }

    /**
     * @return AuthToken
     */
    public static function authenticate()
    {
        return \KolayIK\Auth\Facades\Authorizer::authenticate();
    }

    /**
     * @param $refreshToken
     * @return AuthToken
     */
    public static function refresh()
    {
        return \KolayIK\Auth\Facades\Authorizer::refresh();
    }

    /**
     * @return bool
     */
    public static function invalidate()
    {
        return \KolayIK\Auth\Facades\Authorizer::invalidate();
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return \KolayIK\Auth\Facades\Authorizer::getToken();
    }
}
