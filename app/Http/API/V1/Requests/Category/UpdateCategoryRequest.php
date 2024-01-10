<?php

namespace App\Http\API\V1\Requests\Category;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['string'],
            'description' => ['string','max:255'],
            'parent_id' => [
                Rule::exists('Categories', 'id'),
            ],

        ];
    }
}
