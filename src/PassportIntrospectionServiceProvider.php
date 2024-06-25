<?php

namespace XCoorp\PassportIntrospection;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PassportIntrospectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->configureRoutes();
    }

    /**
     * Configure the routes offered by the application.
     */
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
