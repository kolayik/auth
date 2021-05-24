<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use Exception;
use KolayIK\Auth\Entity\AuthToken;
use KolayIK\Auth\Entity\RefreshToken;
use KolayIK\Auth\Exceptions\KolayAuthException;
use KolayIK\Auth\Exceptions\TokenInvalidException;
use KolayIK\Auth\Model;

/**
 * Class Database
 *
 * @package KolayIK\Auth\Drivers
 */
class Database extends DriverAbstract
{
    /**
     * @param $data
     * @return AuthToken
     */
    private function _map($data): AuthToken
    {
        $authToken = new AuthToken();
        $authToken->setExpirationDate(new Carbon($data->expiration_date));
        $authToken->setToken($data->token);
        $authToken->setUserId($data->user_id);
        $authToken->setCreatedAt(new Carbon($data->created_at));
        $authToken->setUpdatedAt(new Carbon($data->updated_at));

        if (! empty($data->ip_address)) {
            $authToken->setIpAddress($data->ip_address);
        }

        return $authToken;
    }

    /**
     * @param AuthToken $data
     * @return bool|Model\AuthToken
     * @throws Exception
     */
    private function _save(AuthToken $data)
    {
        if (empty($data)) {
            throw new KolayAuthException('Data can not be empty');
        }

        $model = new Model\AuthToken();
        $model->token = $data->getToken();
        $model->user_id = $data->getUserId();
        $model->expiration_date = $data->getExpirationDate();

        if (! $model->save()) {
            return false;
        }

        return $model;
    }

    /**
     * @param RefreshToken $data
     * @return RefreshToken
     *
     * @throws KolayAuthException
     */
    public function saveRefreshToken(RefreshToken $data): RefreshToken
    {
        if (empty($data)) {
            throw new KolayAuthException('Data can not be empty');
        }

        $model = new Model\AuthToken();
        $model->token = $data->getRefreshToken();
        $model->user_id = $data->getUserId();
        $model->ip_address = $data->getIpAddress();
        $model->expiration_date = $data->getExpirationDate();
        $model->created_at = $data->getCreatedAt();
        $model->updated_at = $data->getUpdatedAt();

        if (! $model->save()) {
            throw new KolayAuthException('Data can not be save');
        }

        return $data;
    }

    /**
     * @param $token
     * @return Model\AuthToken
     */
    private function _get($token): Model\AuthToken
    {
        return Model\AuthToken::where('token', $token)->first();
    }

    /**
     * @param $token
     * @return AuthToken
     * @throws Exception
     */
    public function get($token): AuthToken
    {
        $data = $this->_get($token);
        if (! $data instanceof Model\AuthToken || empty($data)) {
            throw new TokenInvalidException('Token not found!');
        }

        return $this->_map($data);
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
        $authToken->setToken($this->generateToken());
        $authToken->setExpirationDate(Carbon::now()->addMinutes($this->getTTL()));

        return $this->_map($this->_save($authToken));
    }

    /**
     * @param $clientRefreshToken
     * @return AuthToken
     * @throws KolayAuthException
     * @throws Exception
     */
    public function refresh($clientRefreshToken): AuthToken
    {
        $validRefreshToken = $this->get($clientRefreshToken);

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
        return Model\AuthToken::query()->where('token', $token)->delete();
    }
}
