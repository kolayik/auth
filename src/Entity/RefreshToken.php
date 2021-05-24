<?php

namespace KolayIK\Auth\Entity;

use Carbon\Carbon;

class RefreshToken
{
    private $refreshToken;
    private $ipAddress;
    private $userId;
    private $expirationDate;
    private $createdAt;
    private $updatedAt;

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
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

    /**
     * @return array
     */
    public function toArray():array {
        return [
            'refreshToken' => $this->refreshToken,
            'userId' => $this->userId,
            'ipAddress' => $this->ipAddress,
            'expirationDate' => $this->expirationDate,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
