<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use KolayIK\Auth\Entity\AuthToken;
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
        $this->getCache()->add($data->getToken(), $data, $this->getTTL());

        return $data;
    }

    /**
     * @param $token
     * @return bool|AuthToken
     */
    private function _get($token)
    {
        return $this->getCache()->get($token);
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
     * @param $token
     * @return bool
     */
    public function invalidate($token)
    {
        return $this->getCache()->destroy($token);
    }
}
