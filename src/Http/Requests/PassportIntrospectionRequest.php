<?php

namespace XCoorp\PassportIntrospection\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassportIntrospectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'introspect' => $this->introspect(),
            default => [],
        };
    }

    protected function introspect(): array
    {
        return [
            'token' => 'string',
            'token_type_hint' => 'nullable|string|in:access_token,refresh_token',
        ];
    }
}
