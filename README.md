<p align="center">
<a href="LICENSE"><img alt="Software License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
<a href="composer.json"><img alt="Laravel Version Requirements" src="https://img.shields.io/badge/laravel-~11.0-gray?logo=laravel&style=flat-square&labelColor=F05340&logoColor=white"></a>
</p>

## Introduction
Laravel Passport Introspection is a Laravel Passport addition that provides an introspection endpoint for your Laravel application.
This is useful if you want to introspect tokens in your application, e.g. to check if a token is still valid or to get information about the token.

You will typically need this if you set up a separate resource server that is meant to authenticate against an Authentication Server running Laravel Passport.
To setup a resource server, you can check out the [Passport Control Package.](https://github.com/xcoorp/laravel-passport-control)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)
- [Code of Conduct](#code-of-conduct)
- [License](#license)

## Installation

> [!IMPORTANT]
> This package assumes you have already installed and configured Laravel Passport in your Laravel application.

You can simply install the package via composer:

```bash
composer require xcoorp/laravel-passport-introspection
```

After the installation you need to add the `introspect` scope to the configured passport `scopes`.
If you haven't already defined scopes or do not know how to do this, please refer to the
[official Laravel Passport documentation](https://laravel.com/docs/11.x/passport#defining-scopes).

```php
use Laravel\Passport\Passport;

Passport::tokensCan([
    'introspect' => 'Introspect tokens',
]);
```

## Usage

Once you have installed the package, a new Route will be available at `/oauth/introspect` that you can use to introspect tokens.
Please note that the introspection endpoint is not meant to be publicly accessible since it can leak sensitive information 
about your tokens. Therefore, this package makes use of the client credentials grant to authenticate the request. More information
on what this is and how to create a client credentials grant client can be found in the 
[official Laravel Passport documentation](https://laravel.com/docs/11.x/passport#client-credentials-grant-tokens).

Once you have created a client credentials grant client, and received an access token for it, you can use the token to authenticate
against the introspection endpoint via Bearer Authentication. The endpoint expects a `POST` request with the following inside the request body (application/x-www-form-urlencoded):

| Parameter                  | Value                                                                                                                                                                                                   |
|----------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| token                      | The token you want to introspect.                                                                                                                                                                       |
| token_type_hint (optional) | The type of token you want to introspect. This can be either `access_token` or `refresh_token`. If you do not provide this parameter, the endpoint will try to introspect the token as an access token. |

The endpoint will return a JSON response with the following parameters:

| Key       | Value                                                                                                                 |
|-----------|-----------------------------------------------------------------------------------------------------------------------|
| active    | A boolean indicating whether the token is active or not                                                               |
| scope     | A JSON string containing a space-separated list of scopes associated with this token                                  |
| client_id | The client id of the client that requested this token.                                                                |
| username  | The unique identifier of the user that requested this token                                                           |
| exp       | Integer timestamp, measured in the number of seconds since January 1 1970 UTC, indicating when this token will expire |

If you want to customize the Route or the Controller that handles the introspection request, you can disable route
publishing of this package and create your own route and controller. You can do this by adding the following line to the 
`boot` method of your `AppServiceProvider`:

```php
public function boot()
{
    PassportIntrospection::ignoreRoutes();
}
```

## Testing

Functionality of this package is tested with [Pest PHP](https://pestphp.com/).
You can run the tests with:

``` bash
composer test
```

## Code of Conduct

In order to ensure that the community is welcoming to all, please review and abide by
the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review the [security policy](SECURITY.md) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
