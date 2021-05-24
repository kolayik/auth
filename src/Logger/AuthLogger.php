<?php

namespace KolayIK\Auth\Logger;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthLogger
 *
 * @package KolayIK\Auth\Logger
 */
class AuthLogger
{
    /** @var bool */
    private $loggerStatus;

    /**
     * AuthLogger constructor.
     *
     * @param bool $loggerStatus
     */
    public function __construct(bool $loggerStatus)
    {
        $this->loggerStatus = $loggerStatus;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = [])
    {
        App::terminating(function () use ($message, $context) {
            if ($this->loggerStatus) {
                Log::info($message, $context);
            }
        });
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert(string $message, array $context = [])
    {
        App::terminating(function () use ($message, $context) {
            if ($this->loggerStatus) {
                Log::alert($message, $context);
            }
        });
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = [])
    {
        App::terminating(function () use ($message, $context) {
            if ($this->loggerStatus) {
                Log::error($message, $context);
            }
        });
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = [])
    {
        App::terminating(function () use ($message, $context) {
            if ($this->loggerStatus) {
                Log::debug($message, $context);
            }
        });
    }
}
