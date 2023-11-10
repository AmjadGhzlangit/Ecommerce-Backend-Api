<?php

namespace App\Http\API\V1\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['max:255'],
            'last_name' => ['max:255'],
            'email' => ['max:255', 'email'],
            'phone' => ['phone:AUTO'],
            'country_id' => [
                Rule::exists('countries', 'id'),
            ],
            'password' => ['min:6'],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

        ];
    }
}
