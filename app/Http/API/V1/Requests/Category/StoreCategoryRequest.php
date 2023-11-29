<?php

namespace App\Http\API\V1\Requests\Category;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['required' | 'max:255'],
            'parent_id' => [
                Product::exists('Product', 'id'),
            ],
        ];
    }
}
