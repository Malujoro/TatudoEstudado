<?php

namespace Database\Factories;

use App\Models\Assunto;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssuntoFactory extends Factory
{
    protected $model = Assunto::class;

    public function definition(): array
    {
        return [
            'nome'              => $this->faker->words(2, true),
            'materia_id'        => Materia::factory(),
            'teoria_finalizada' => false,
            'tipo'              => ['teoria', 'exercicio'],
        ];
    }
}
