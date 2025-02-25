<?php

namespace XCoorp\PassportIntrospection\Factories;

use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use XCoorp\PassportIntrospection\Contracts\IntrospectionResponseFactory as IntrospectionResponseFactoryContract;
use XCoorp\PassportIntrospection\Enums\CredentialType;
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
            'active' => $token->revoked === false,
            'client_id' => $accessToken?->client_id,
            'username' => $accessToken?->user?->email ? $accessToken->user->email : null,
            'sub' => $accessToken?->user_id,
            'scope' => implode(' ', $accessToken?->scopes),
            'credential_type' => $accessToken?->client?->personal_access_client ? CredentialType::PersonalAccess : (
                $accessToken?->client?->password_client ? CredentialType::Password : (
                    !$accessToken?->client?->secret ? CredentialType::PKCE : (
                        $accessToken?->client?->redirect && $accessToken->client->redirect === '' ? CredentialType::ClientCredentials : (
                            $accessToken?->client?->redirect && $accessToken->client->redirect !== '' ? CredentialType::AuthorizationCode : CredentialType::Unknown
                        )
                    )
                )
            ),
            'exp' => $token?->expires_at?->getTimestamp(),
            'iat' => $token?->created_at?->getTimestamp(),
            'nbf' => $token?->created_at?->getTimestamp(),
        ]);
    }
}
