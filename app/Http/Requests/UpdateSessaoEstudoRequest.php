<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for updating a study session.
 */
class UpdateSessaoEstudoRequest extends FormRequest
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
            'data' => ['sometimes', 'date'],
            'tipo' => ['sometimes', 'string', 'max:255'],
            'horas' => ['sometimes', 'numeric', 'min:0'],
            'finalizado' => ['sometimes', 'boolean'],
        ];
    }
}
