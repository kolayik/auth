<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use KolayIK\Auth\Entity\AuthToken;
use KolayIK\Auth\Entity\RefreshToken;
use KolayIK\Auth\Providers\Storage\Illuminate;
use KolayIK\Auth\Exceptions\KolayAuthException;
use KolayIK\Auth\Exceptions\TokenInvalidException;

class Cache extends DriverAbstract implements DriverInterface
{
    /**
     * @param AuthToken $data
     * @return bool|AuthToken
     * @throws \Exception
     */
    private function _save(AuthToken $data)
    {
        if (empty($data)) {
            throw new KolayAuthException('Data can not be empty');
        }
        $this->getCache()->add(Illuminate::TOKEN_PREFIX, $data->getToken(), $data, $this->getTTL());

        return $data;
    }

    /**
     * @param RefreshToken $data
     * @return bool|RefreshToken
     * @throws \Exception
     */
    private function _saveRefreshToken(RefreshToken $data)
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
     * @param $token
     * @return bool|AuthToken
     */
    private function _getRefreshToken($refreshToken)
    {
        return $this->getCache()->get(Illuminate::REFRESH_TOKEN_PREFIX, $refreshToken);
    }

    /**
     * @param $token
     * @return AuthToken
     * @throws \Exception
     */
    public function get($token)
    {
        $data = $this->_get($token);
        if (!$data instanceof AuthToken || empty($data)) {
            throw new TokenInvalidException('Token not found!');
        }
        return $data;
    }

    /**
     * @param $userId
     * @return bool|AuthToken
     */
    public function generate($userId)
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
     * @param $userId
     * @return bool|RefreshToken
     */
    public function generateRefreshToken($userId)
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setRefreshToken(parent::generateToken());
        $refreshToken->setIpAddress(\Request::ip());
        $refreshToken->setUserId($userId);

        $now = Carbon::now();
        $expirationDate = clone $now;
        $refreshToken->setExpirationDate($expirationDate->addMinutes($this->getRefreshTTL()));
        $refreshToken->setCreatedAt($now);
        $refreshToken->setUpdatedAt($now);

        return $this->_saveRefreshToken($refreshToken);
    }

    /**
     * @param $clientRefreshToken
     * @return bool|AuthToken
     * @throws \Exception
     */
    public function refresh($clientRefreshToken)
    {
        $validRefreshToken = $this->_getRefreshToken($clientRefreshToken);

        if (
            empty($validRefreshToken) ||
            $validRefreshToken->isExpired() ||
            ($validRefreshToken->getIpAddress() != \Request::ip())
        ) {
            throw new KolayAuthException('Invalid login information, please log in again.', 401);
        }

        return $this->generate($validRefreshToken->getUserId());
    }

    /**
     * @param $token
     * @return bool
     */
    public function invalidate($token)
    {
        return $this->getCache()->destroy($token);
    }
}
