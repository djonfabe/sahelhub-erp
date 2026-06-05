<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class HelpdeskTicketFactory extends Factory
{
    protected $model = HelpdeskTicket::class;

    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status'      => 'open',
            'priority'    => 'medium',
            'category_id' => null,
            'created_by'  => null,
        ];
    }
}
