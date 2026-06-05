<?php

namespace Database\Factories;

use App\Models\HelpdeskReply;
use App\Models\HelpdeskTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class HelpdeskReplyFactory extends Factory
{
    protected $model = HelpdeskReply::class;

    public function definition(): array
    {
        return [
            'ticket_id'   => HelpdeskTicket::factory(),
            'message'     => fake()->paragraph(),
            'is_internal' => false,
            'created_by'  => null,
        ];
    }
}
