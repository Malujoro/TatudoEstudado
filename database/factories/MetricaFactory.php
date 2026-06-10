<?php

namespace Database\Factories;

use App\Models\Assunto;
use App\Models\Metrica;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetricaFactory extends Factory
{
    protected $model = Metrica::class;

    public function definition(): array
    {
        return [
            'acertos' => $this->faker->numberBetween(0, 50),
            'erros' => $this->faker->numberBetween(0, 20),
            'assunto_id' => Assunto::factory(),
        ];
    }
}
