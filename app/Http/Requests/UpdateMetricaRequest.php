<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para atualização de métrica.
 */
class UpdateMetricaRequest extends FormRequest
{
    /**
     * No momento, a autorização é controlada por middleware de rota (`auth`).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'acertos' => ['sometimes', 'integer', 'min:0'],
            'erros' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
