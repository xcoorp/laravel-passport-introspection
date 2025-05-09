<?php

namespace XCoorp\PassportIntrospection\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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

    public function introspect(PassportIntrospectionRequest $request): PassportIntrospectionResource
    {
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
