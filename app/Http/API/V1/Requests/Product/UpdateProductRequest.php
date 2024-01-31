<?php

namespace App\Http\API\V1\Requests\Product;


use App\Enums\CurrencyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function rules()
    {
        return [


            'name' => ['string'],
            'description' => ['string'],
            'image' => ['image','mimes:png,jpg,jpeg'],
             'category_id' => [
                Rule::exists('categories', 'id'),
             ],
             'qty' => ['integer'],
             'price' => ['numeric'],
             'currency' => [


                Rule::enum(CurrencyType::class),
             ],

        ];


    }
}
