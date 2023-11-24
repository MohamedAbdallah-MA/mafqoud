<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class MissingPeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name"=> fake()->name(),
            "image"=> fake()->imageUrl(),
            // "gender" => rand();
        ];
    }
}
