<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for updating an assunto.
 */
class UpdateAssuntoRequest extends FormRequest
{
    /**
     * Authorize the request.
     *
     * Currently handled by route middleware (`auth`).
     *
     * @return bool True when the request is allowed to proceed.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     *
     * Expected payload (partial update):
     * - nome: string (optional)
     * - teoria_finalizada: bool (optional)
     * - tipo: array<string> (optional; if present, min 1, max 3)
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'teoria_finalizada' => ['sometimes', 'boolean'],
            'tipo' => ['sometimes', 'required', 'array', 'min:1', 'max:3'],
            'tipo.*' => ['required', 'string', 'in:teoria,exercicio,revisao', 'distinct'],
        ];
    }
}
