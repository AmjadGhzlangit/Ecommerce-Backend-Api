<?php

namespace App\Http\API\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string','max:255','min:3'],
            'last_name' => ['required', 'string','max:255','min:3'],
            'email' => ['required_if:phone,=,null'],
            'phone' => ['required_if:email,=,null'],
            'country_id' => [
                Rule::exists('countries', 'id'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'udid' => ['string'],
            'fcm_token' => ['required_with:udid'],
        ];
    }
}
