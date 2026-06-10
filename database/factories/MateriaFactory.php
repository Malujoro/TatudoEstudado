<?php

namespace Database\Factories;

use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaFactory extends Factory
{
    protected $model = Materia::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(3, true),
            'user_id' => User::factory(),
        ];
    }
}
