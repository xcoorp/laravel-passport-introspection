<?php

namespace XCoorp\PassportIntrospection\Factories;

use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use XCoorp\PassportIntrospection\Contracts\IntrospectionResponseFactory as IntrospectionResponseFactoryContract;
use XCoorp\PassportIntrospection\Http\Resources\PassportIntrospectionResource;

class IntrospectionResponseFactory implements IntrospectionResponseFactoryContract
{
    /**
     * {@inheritDoc}
     *
     * @param Token|RefreshToken|null $token
     * @param Token|null $accessToken
     */
    public function createResponse($token, $accessToken): PassportIntrospectionResource
    {
        if (! $token) {
            return new PassportIntrospectionResource([
                'active' => false,
            ]);
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return new PassportIntrospectionResource([
            'active' => $token->revoked === false && $token->expires_at?->isFuture() && $token->created_at?->isPast(),
            'client_id' => $accessToken?->client_id,
            'username' => $accessToken?->user?->email ? $accessToken->user->email : null,
            'sub' => $accessToken?->user_id ?? $accessToken?->client_id,
            'scope' => implode(' ', $accessToken?->scopes),
            'exp' => $token?->expires_at?->getTimestamp(),
            'iat' => $token?->created_at?->getTimestamp(),
            'nbf' => $token?->created_at?->getTimestamp(),
        ]);
    }
}
