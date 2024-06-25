<?php

namespace XCoorp\PassportIntrospection;

class PassportIntrospection
{
    /**
     * Indicates if Introspection routes will be registered.
     */
    public static bool $registersRoutes = true;

    /**
     * Configure Passport Introspection to not register its routes.
     */
    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }
}
