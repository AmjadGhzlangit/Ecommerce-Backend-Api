<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return $categoriesData = [
            [
                'parent_id' => null,
                'name' => 'Category 1',
                'description' => 'Description for Category 1',
            ],
            [
                'parent_id' => null,
                'name' => 'Category 2',
                'description' => 'Description for Category 2',
            ],
        ];

        foreach ($categoriesData as $categoryData) {
            Category::create($categoryData);
        }
    }
}