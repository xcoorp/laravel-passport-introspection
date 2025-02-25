<?php

namespace XCoorp\PassportIntrospection;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use XCoorp\PassportIntrospection\Factories\IntrospectionResponseFactory;
use XCoorp\PassportIntrospection\Contracts\IntrospectionResponseFactory as IntrospectionResponseFactoryContract;

class PassportIntrospectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IntrospectionResponseFactoryContract::class, function () {
            return new IntrospectionResponseFactory();
        });
    }

    public function boot(): void
    {
        $this->configureRoutes();
    }

    protected function configureRoutes(): void
    {
        if (PassportIntrospection::$registersRoutes) {
            Route::group([
                'namespace' => 'XCoorp\PassportIntrospection\Http\Controllers',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
            });
        }
    }
}
