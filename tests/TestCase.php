<?php

namespace XCoorp\PassportIntrospection\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use XCoorp\PassportIntrospection\PassportIntrospectionServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function getPackageProviders($app): array
    {
        return [
            PassportIntrospectionServiceProvider::class,
        ];
    }
}
