<?php

namespace KolayIK\Auth\Providers;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../../config/config.php');
        $this->publishes([$path => config_path('kolayauth.php')], 'config');
        $this->publishes([__DIR__ . '/../Database/Migrations' => $this->app->databasePath() . '/migrations'], 'migrations');

        $this->mergeConfigFrom($path, 'kolayauth');

        $this->aliasMiddleware();
    }

    /**
     * Alias the middleware.
     *
     * @return void
     */
    protected function aliasMiddleware()
    {
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }
}
