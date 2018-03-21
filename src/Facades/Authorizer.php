<?php

namespace KolayIK\Auth\Facades;

use Illuminate\Support\Facades\Facade;

class Authorizer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kolay-auth';
    }
}
