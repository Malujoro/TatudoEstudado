<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for creating a study session.
 */
class StoreSessaoEstudoRequest extends FormRequest
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
            'data' => ['required', 'date'],
            'tipo' => ['required', 'string', 'max:255'],
            'horas' => ['required', 'numeric', 'min:0'],
            'finalizado' => ['sometimes', 'boolean'],
            'assunto_id' => ['required', 'uuid', 'exists:assuntos,id'],
        ];
    }
}
