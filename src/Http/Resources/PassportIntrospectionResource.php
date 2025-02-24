<?php

namespace XCoorp\PassportIntrospection\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassportIntrospectionResource extends JsonResource
{
    /**
     * {@inheritdoc}
     */
    public static $wrap = null;

    /**
     * {@inheritDoc}
     */
    public function toArray(Request $request): array
    {
        return [
            'active' => $this->when(isset($this['active']), fn () => $this['active']),
            'client_id' => $this->when(isset($this['client_id']), fn () => $this['client_id']),
            'username' => $this->when(isset($this['username']), fn () => $this['username']),
            'scope' => $this->when(isset($this['scope']), fn () => $this['scope']),
            'credential_type' => $this->when(isset($this['credential_type']), fn () => $this['credential_type']->value),
            'token_type' => 'bearer',
            'sub' => $this->when(isset($this['sub']), fn () => $this['sub']),
            'exp' => $this->when(isset($this['exp']), fn () => $this['exp']),
            'iat' => $this->when(isset($this['iat']), fn () => $this['iat']),
            'nbf' => $this->when(isset($this['nbf']), fn () => $this['nbf']),
        ];
    }
}
