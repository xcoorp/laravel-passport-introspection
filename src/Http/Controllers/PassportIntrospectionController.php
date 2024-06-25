<?php

namespace XCoorp\PassportIntrospection\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Passport\Exceptions\AuthenticationException;
use Laravel\Passport\Passport;
use XCoorp\PassportIntrospection\Http\Requests\PassportIntrospectionRequest;
use XCoorp\PassportIntrospection\Http\Resources\PassportIntrospectionResource;

class PassportIntrospectionController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Handle a request to introspect a token.
     *
     * @throws AuthenticationException
     */
    public function introspect(PassportIntrospectionRequest $request): PassportIntrospectionResource
    {
        // Laravel passport client credentials grant middleware, does check for the bearer token
        // to be valid and of the valid scope, but it does not check if it is actually from a client
        // credential only client. So we need to check that here, since this endpoint is only for
        // client credentials grant clients. NOTE: Signature, expiration and validity checks are not needed, since the middleware
        // already does that.
        $jwtPayload = json_decode(base64_decode(explode('.', $request->bearerToken())[1]), true);
        if (! $jwtPayload || ! isset($jwtPayload['jti'])) {
            throw new AuthenticationException();
        }

        $client = Passport::client()->where('id', $jwtPayload['aud'])->first();
        if (! $client || $client->personal_access_client || $client->password_client || $client->redirect !== '') {
            throw new AuthenticationException();
        }

        $data = $request->validated();

        if (! isset($data['token'])) {
            return new PassportIntrospectionResource([
                'active' => false,
            ]);
        }

        $type = $data['token_type_hint'] ?? 'access_token';
        if ($type === 'access_token') {
            $token = Passport::token()->where('id', $data['token'])->first();
            $accessToken = $token;
        } else {
            $token = Passport::refreshToken()->where('id', $data['token'])->first();
            $accessToken = Passport::token()->where('id', $token?->access_token_id)->first();
        }

        if (! $token) {
            return new PassportIntrospectionResource([
                'active' => false,
            ]);
        }

        return new PassportIntrospectionResource([
            'active' => $token->revoked === false,
            'client_id' => $accessToken?->client_id,
            'username' => $accessToken?->user_id,
            'scope' => implode(' ', $accessToken?->scopes),
            'exp' => $token?->expires_at?->getTimestamp(),
        ]);
    }
}
