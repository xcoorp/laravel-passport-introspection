<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use XCoorp\PassportIntrospection\Http\Controllers\PassportIntrospectionController;

Route::post('/oauth/introspect', [PassportIntrospectionController::class, 'introspect'])->middleware(['api', CheckClientCredentials::class.':introspect']);
