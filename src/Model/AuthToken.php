<?php

namespace KolayIK\Auth\Model;

class AuthToken extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'authtoken';
    protected $primaryKey = 'token';
    public $timestamps = true;
    public $incrementing = false;
}