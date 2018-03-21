<?php

namespace KolayIK\Auth\Drivers;

use Carbon\Carbon;
use KolayIK\Auth\Entity\AuthToken;
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
     * @param $token
     * @return bool
     */
    public function invalidate($token)
    {
        return Model\AuthToken::where('token', $token)->delete();
    }
}
