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

        $client = $accessToken?->client;
        $type = match (true) {
            $client?->personal_access_client => CredentialType::PersonalAccess,
            $client?->password_client => CredentialType::Password,
            !$client?->secret => CredentialType::PKCE,
            $client->redirect === '' => CredentialType::ClientCredentials,
            $client?->redirect !== null && $client->redirect !== '' => CredentialType::AuthorizationCode,
            default => CredentialType::Unknown,
        };

        /** @noinspection PhpUndefinedFieldInspection */
        return new PassportIntrospectionResource([
            'active' => $token->revoked === false && $token->expires_at?->isFuture() && $token->created_at?->isPast(),
            'client_id' => $accessToken?->client_id,
            'username' => $accessToken?->user?->email ? $accessToken->user->email : null,
            'sub' => $type === CredentialType::ClientCredentials ? $accessToken?->client_id : $accessToken?->user_id,
            'scope' => implode(' ', $accessToken?->scopes),
            'credential_type' => $type,
            'exp' => $token?->expires_at?->getTimestamp(),
            'iat' => $token?->created_at?->getTimestamp(),
            'nbf' => $token?->created_at?->getTimestamp(),
        ]);
    }
}
