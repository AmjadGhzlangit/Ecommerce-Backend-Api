<?php

namespace App\Http\API\V1\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'description' => ['required', 'string'],
            'parent_id' => [
                Rule::exists('categories', 'id'),
            ],
        ];
    }
}
