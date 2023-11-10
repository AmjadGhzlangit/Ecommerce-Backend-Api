<?php

namespace App\Http\API\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RefreshRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'access_token' => ['required'],
        ];
    }
}
