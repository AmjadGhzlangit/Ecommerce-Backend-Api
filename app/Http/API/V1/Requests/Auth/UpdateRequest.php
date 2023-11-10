<?php

namespace App\Http\API\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['string','max:255'],
            'last_name' => ['string','max:255'],
            'email' => ['max:255', 'email'],
            'phone' => ['string'],
            'country_id' => [
                Rule::exists('countries', 'id'),
            ],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
