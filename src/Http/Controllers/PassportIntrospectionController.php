<?php

namespace XCoorp\PassportIntrospection\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Passport\Exceptions\AuthenticationException;
use Laravel\Passport\Passport;
use XCoorp\PassportIntrospection\Contracts\IntrospectionResponseFactory;
use XCoorp\PassportIntrospection\Http\Requests\PassportIntrospectionRequest;
use XCoorp\PassportIntrospection\Http\Resources\PassportIntrospectionResource;

class PassportIntrospectionController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected IntrospectionResponseFactory $responseFactory;

    public function __construct(IntrospectionResponseFactory $responseFactory = null)
    {
        $this->responseFactory = $responseFactory ?? app(IntrospectionResponseFactory::class);
    }

    /**
     * @throws AuthenticationException
     */
    public function introspect(PassportIntrospectionRequest $request): PassportIntrospectionResource
    {
        // Laravel passport client credentials grant middleware, does check for the bearer token
        // to be valid and of the valid scope, but it does not check if it is actually from a client
        // credential only client. So we need to check that here, since this endpoint is only for
        // client credentials grant clients. NOTE: Signature, expiration and validity checks are not needed, since the middleware
        // already does that for us.
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
            return $this->responseFactory->createResponse(null, null);
        }

        $type = $data['token_type_hint'] ?? 'access_token';
        if ($type === 'access_token') {
            $token = Passport::token()->where('id', $data['token'])->first();
            $accessToken = $token;
        } else {
            $token = Passport::refreshToken()->where('id', $data['token'])->first();
            $accessToken = Passport::token()->where('id', $token?->access_token_id)->first();
        }

        return $this->responseFactory->createResponse($token, $accessToken);
    }
}
