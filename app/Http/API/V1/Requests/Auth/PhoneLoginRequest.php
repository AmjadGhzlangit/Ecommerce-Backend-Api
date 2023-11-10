<?php

namespace App\Http\API\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required',Rule::exists('users', 'phone')],
            'password' => ['required', 'min:6'],
            'udid' => ['string'],
            'fcm_token' => ['required_with:udid'],
        ];
    }
}
