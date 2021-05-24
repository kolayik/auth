<?php

namespace KolayIK\Auth\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string token
 * @property string user_id
 * @property Carbon expiration_date
 * @property string ip_address
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @mixin Builder
 */
class AuthToken extends Model
{
    protected $table = 'authtoken';

    protected $primaryKey = 'token';

    public $timestamps = true;

    public $incrementing = false;
}
