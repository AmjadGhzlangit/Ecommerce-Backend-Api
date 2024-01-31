<?php

namespace App\Http\API\V1\Requests\Product;

use App\Enums\CurrencyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
                    'name' => ['required','string'],
                    'description' => ['required','string'],
                    'image' => ['image', 'mimes:png,jpg,jpeg', 'max:2048'],
                    'category_id' => [
                        Rule::exists('categories', 'id'),
                     ],

                     'qty' => ['required', 'integer','min:0'],
                     'price' => ['required', 'numeric','min:0'],

                     'currency' => [
                        'required',

                        Rule::enum(CurrencyType::class),
                     ],
                  ];


    }
}