<?php

namespace XCoorp\PassportIntrospection\Contracts;

use Laravel\Passport\Token;

interface IntrospectionResponseFactory
{
    /**
     * Create a token introspection response.
     *
     * @param mixed $token          The token requested (access or refresh)
     * @param mixed $accessToken    The associated access token
     */
    public function createResponse($token, $accessToken);
}
