<?php

namespace Database\Factories;

use App\Models\Assunto;
use App\Models\SessaoEstudo;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessaoEstudoFactory extends Factory
{
    protected $model = SessaoEstudo::class;

    public function definition(): array
    {
        return [
            'data'       => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'tipo'       => $this->faker->randomElement(['teoria', 'exercicio', 'revisao']),
            'horas'      => $this->faker->randomFloat(1, 0.5, 4.0),
            'finalizado' => false,
            'assunto_id' => Assunto::factory(),
        ];
    }
}
