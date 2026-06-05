<?php

namespace Database\Factories;

use App\Models\HelpdeskCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class HelpdeskCategoryFactory extends Factory
{
    protected $model = HelpdeskCategory::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->words(2, true),
            'description' => fake()->sentence(),
            'color'       => '#3B82F6',
            'is_active'   => true,
            'creator_id'  => null,
            'created_by'  => null,
        ];
    }
}
