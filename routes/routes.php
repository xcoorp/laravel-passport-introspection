<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
use XCoorp\PassportIntrospection\Http\Controllers\PassportIntrospectionController;

Route::post('/oauth/introspect', [PassportIntrospectionController::class, 'introspect'])->middleware(['api', EnsureClientIsResourceOwner::class.':introspect']);
