<?php

namespace KolayIK\Auth\Providers;

use Illuminate\Support\ServiceProvider;

use KolayIK\Auth\Authorizer;
use KolayIK\Auth\Drivers\DriverInterface;
use KolayIK\Auth\Http\Middleware\Authenticate;
use KolayIK\Auth\Http\Parser\AuthHeaders;
use KolayIK\Auth\Http\Parser\Cookies;
use KolayIK\Auth\Http\Parser\InputSource;
use KolayIK\Auth\Http\Parser\Parser;
use KolayIK\Auth\Http\Parser\QueryString;
use KolayIK\Auth\Http\Parser\RouteParams;
use KolayIK\Auth\Providers\Auth\AuthInterface;
use KolayIK\Auth\Providers\Storage\StorageInterface;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * The middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'kolay.auth' => Authenticate::class
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCacheProvider();
        $this->registerAliases();

        $this->registerDriver();
        $this->registerTokenParser();
        $this->registerManager();
    }

    /**
     * Bind some aliases.
     *
     * @return void
     */
    protected function registerAliases()
    {
        $this->app->alias(Authorizer::class, 'kolay-auth');
        $this->app->alias('kolay-auth-provider-cache', StorageInterface::class);
        $this->app->alias('kolay-auth-driver', DriverInterface::class);
        $this->app->alias('kolay-auth-parser', Parser::class);
    }

    protected function registerManager()
    {
        // create image
        $this->app->singleton(Authorizer::class, function ($app) {
            return new Authorizer(
                $this->getAllConfig(),
                $app['kolay-auth-parser'],
                $app['kolay-auth-driver']
            );
        });
    }

    /**
     * @param $driverName
     * @return bool|DriverInterface
     */
    private function _getDriver($driverName)
    {
        if (is_string($driverName)) {
            $driverClass = sprintf('KolayIK\\Auth\\Drivers\\%s', $driverName);

            if (class_exists($driverClass)) {
                $driver = new $driverClass();
                return $driver;
            }
        }

        return false;
    }

    /**
     * Register the bindings for the Storage provider.
     *
     * @return void
     */
    protected function registerCacheProvider()
    {
        $this->app->singleton('kolay-auth-provider-cache', function () {
            return $this->getConfigInstance('providers.cache');
        });
    }

    /**
     * Register the bindings for the Driver.
     */
    protected function registerDriver()
    {
        // create image
        $this->app->singleton('kolay-auth-driver', function ($app) {
            $driverName = ucfirst($this->getConfig('driver'));
            $driverClass = $this->_getDriver($driverName);

            if ($driverClass) {
                $driverClass->setCache($this->app['kolay-auth-provider-cache']);
                $driverClass->setConfig($this->getAllConfig());
                return $driverClass;
            }

            throw new \Exception("Driver ({$driverName}) could not be instantiated");
        });
    }

    /**
     * Register the bindings for the Token Parser.
     *
     * @return void
     */
    protected function registerTokenParser()
    {
        $this->app->singleton('kolay-auth-parser', function ($app) {
            $parser = new Parser(
                $app['request'],
                [
                    new AuthHeaders(),
                    new QueryString(),
                    new InputSource(),
                    new RouteParams(),
                    new Cookies(),
                ]
            );

            $app->refresh('request', $parser, 'setRequest');

            return $parser;
        });
    }

    /**
     * @return array
     */
    protected function getAllConfig()
    {
        return config("kolayauth");
    }

    /**
     * Helper to get the config values.
     *
     * @param  string $key
     * @param  string $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return config(sprintf("kolayauth.%s", $key), $default);
    }

    /**
     * Get an instantiable configuration instance.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    protected function getConfigInstance($key)
    {
        $instance = $this->getConfig($key);

        if (is_string($instance)) {
            return $this->app->make($instance);
        }

        return $instance;
    }
}
