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
            'username' => $this->when(isset($this['user_id']), fn () => $this['username']),
            'scope' => $this->when(isset($this['scope']), fn () => $this['scope']),
            'exp' => $this->when(isset($this['exp']), fn () => $this['exp']),
        ];
    }
}
