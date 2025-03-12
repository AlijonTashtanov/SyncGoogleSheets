<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Record;

class RecordFactory extends Factory
{
    protected $model = Record::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'status' => $this->faker->randomElement(['Allowed', 'Prohibited']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
