<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use Exception;
use KolayIK\Auth\Entity\AuthToken;
use KolayIK\Auth\Entity\RefreshToken;
use KolayIK\Auth\Providers\Storage\Illuminate;
use KolayIK\Auth\Exceptions\KolayAuthException;
use KolayIK\Auth\Exceptions\TokenInvalidException;

/**
 * Class Cache
 *
 * @package KolayIK\Auth\Drivers
 */
class Cache extends DriverAbstract
{
    /**
     * @param AuthToken $data
     * @return AuthToken
     * @throws Exception
     */
    private function _save(AuthToken $data): AuthToken
    {
        if (empty($data)) {
            throw new KolayAuthException('Data can not be empty');
        }
        $this->getCache()->add(Illuminate::TOKEN_PREFIX, $data->getToken(), $data, $this->getTTL());

        return $data;
    }

    /**
     * @param RefreshToken $data
     * @return RefreshToken
     * @throws Exception
     */
    public function saveRefreshToken(RefreshToken $data): RefreshToken
    {
        if (empty($data)) {
            throw new KolayAuthException('Data can not be empty');
        }

        $this->getCache()->add(Illuminate::REFRESH_TOKEN_PREFIX, $data->getRefreshToken(), $data, $this->getRefreshTTL());

        return $data;
    }

    /**
     * @param $token
     * @return bool|AuthToken
     */
    private function _get($token)
    {
        return $this->getCache()->get(Illuminate::TOKEN_PREFIX, $token);
    }

    /**
     * @param $refreshToken
     * @return bool|AuthToken
     */
    private function _getRefreshToken($refreshToken)
    {
        return $this->getCache()->get(Illuminate::REFRESH_TOKEN_PREFIX, $refreshToken);
    }

    /**
     * @param $token
     * @return AuthToken
     * @throws Exception
     */
    public function get($token): AuthToken
    {
        $data = $this->_get($token);
        if (! $data instanceof AuthToken || empty($data)) {
            throw new TokenInvalidException('Token not found!');
        }

        return $data;
    }

    /**
     * @param $userId
     * @return AuthToken
     * @throws Exception
     */
    public function generate($userId): AuthToken
    {
        $authToken = new AuthToken();
        $authToken->setUserId($userId);
        $authToken->setToken(parent::generateToken());

        $now = Carbon::now();
        $expirationDate = clone $now;
        $authToken->setExpirationDate($expirationDate->addMinutes($this->getTTL()));
        $authToken->setCreatedAt($now);
        $authToken->setUpdatedAt($now);

        return $this->_save($authToken);
    }

    /**
     * @param $clientRefreshToken
     * @return AuthToken
     * @throws Exception
     */
    public function refresh($clientRefreshToken): AuthToken
    {
        $validRefreshToken = $this->_getRefreshToken($clientRefreshToken);

        if (empty($validRefreshToken) || $validRefreshToken->isExpired()) {
            throw new KolayAuthException('Invalid login information, please log in again.', 401);
        }

        return $this->generate($validRefreshToken->getUserId());
    }

    /**
     * @param $token
     * @return bool
     */
    public function invalidate($token): bool
    {
        return $this->getCache()->destroy($token);
    }
}
