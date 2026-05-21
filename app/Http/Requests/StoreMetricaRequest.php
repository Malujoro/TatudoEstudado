<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for creating a metrica.
 */
class StoreMetricaRequest extends FormRequest
{
    /**
     * Authorize the request.
     *
     * Currently handled by route middleware (`auth`).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
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
