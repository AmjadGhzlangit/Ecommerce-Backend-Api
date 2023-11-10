<?php

namespace App\Http\API\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required','string'],
            'udid' => ['string'],
        ];
    }
}
