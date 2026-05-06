<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de métrica.
 */
class StoreMetricaRequest extends FormRequest
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
            'assunto_id' => ['required', 'uuid', 'exists:assuntos,id', 'unique:metricas,assunto_id'],
            'acertos' => ['sometimes', 'integer', 'min:0'],
            'erros' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
