<?php

namespace App\Http\API\V1\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
                    'name' => ['required','string'],
                    'description' => ['required','string','max:255'],
                    'image' => ['image','mimes:png,jpg,jpeg'],
                     'category_id' => [
                        Rule::exists('categories', 'id'),
                     ],


                ];

    }
}
