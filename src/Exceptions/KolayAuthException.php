<?php

namespace KolayIK\Auth\Exceptions;

use Exception;

class KolayAuthException extends Exception
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'An error occurred';
}
