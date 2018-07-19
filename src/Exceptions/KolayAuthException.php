<?php

namespace KolayIK\Auth\Exceptions;

use Exception;

class KolayAuthException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public $details;
    protected $message = 'An error occurred';

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}
