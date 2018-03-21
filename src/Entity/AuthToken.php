<?php

namespace KolayIK\Auth\Entity;

use Carbon\Carbon;

class AuthToken
{
    private $token;
    private $userId;
    private $expirationDate;
    private $createdAt;
    private $updatedAt;


    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Carbon
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param Carbon $expiration
     */
    public function setExpirationDate(Carbon $expiration)
    {
        $this->expirationDate = $expiration;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     */
    public function setCreatedAt(Carbon $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param Carbon $updatedAt
     */
    public function setUpdatedAt(Carbon $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return Carbon::now()->gte($this->getExpirationDate());
    }
}