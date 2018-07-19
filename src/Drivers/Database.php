<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use KolayIK\Auth\Entity\AuthToken;
use KolayIK\Auth\Entity\RefreshToken;
use KolayIK\Auth\Exceptions\KolayAuthException;
use KolayIK\Auth\Exceptions\TokenInvalidException;
use KolayIK\Auth\Model;

class Database extends DriverAbstract implements DriverInterface
{
    /**
     * @param $data
     * @return AuthToken
     */
    private function _map($data)
    {
        $authToken = new AuthToken();
        $authToken->setExpirationDate(new Carbon($data->expiration_date));
        $authToken->setToken($data->token);
        $authToken->setUserId($data->user_id);
        $authToken->setCreatedAt(new Carbon($data->created_at));
        $authToken->setUpdatedAt(new Carbon($data->updated_at));

        if (!empty($data->ip_address)) {
            $authToken->setIpAddress($data->ip_address);
        }

        return $authToken;
    }

    /**
     * @param AuthToken $data
     * @return bool|Model\AuthToken
     * @throws \Exception
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

        if (!$model->save()) {
            return false;
        }

        return $model;
    }

    /**
     * @param RefreshToken $data
     * @return bool|Model\RefreshToken
     * @throws \Exception
     */
    private function _saveRefreshToken(RefreshToken $data)
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

        if (!$model->save()) {
            return false;
        }

        return $data;
    }

    /**
     * @param $token
     * @return Model\AuthToken
     */
    private function _get($token)
    {
        return Model\AuthToken::where('token', $token)->first();
    }

    /**
     * @param $token
     * @return AuthToken
     * @throws \Exception
     */
    public function get($token)
    {
        $data = $this->_get($token);
        if (!$data instanceof Model\AuthToken || empty($data)) {
            throw new TokenInvalidException('Token not found!');
        }
        return $this->_map($data);
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
        $authToken->setExpirationDate(Carbon::now()->addMinutes($this->getTTL()));

        return $this->_map($this->_save($authToken));
    }

    /**
     * @param $clientRefreshToken
     * @return bool|AuthToken
     * @throws \Exception
     */
    public function refresh($clientRefreshToken)
    {
        $validRefreshToken = $this->get($clientRefreshToken);

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
     * @param $token
     * @return bool
     */
    public function invalidate($token)
    {
        return Model\AuthToken::where('token', $token)->delete();
    }
}
