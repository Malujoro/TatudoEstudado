<?php

namespace Database\Factories;

use App\Models\Assunto;
use App\Models\Caderno;
use Illuminate\Database\Eloquent\Factories\Factory;

class CadernoFactory extends Factory
{
    protected $model = Caderno::class;

    public function definition(): array
    {
        return [
            'conteudo'   => $this->faker->paragraph(),
            'assunto_id' => Assunto::factory(),
        ];
    }
}
