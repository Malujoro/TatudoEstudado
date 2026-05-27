<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for creating an assunto.
 */
class StoreAssuntoRequest extends FormRequest
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
     * Expected payload:
     * - nome: string
     * - materia_id: uuid
     * - teoria_finalizada: bool (optional)
     * - tipo: array<string> (min 1, max 3)
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'materia_id' => ['required', 'uuid', 'exists:materias,id'],
            'teoria_finalizada' => ['sometimes', 'boolean'],
            'tipo' => ['required', 'array', 'min:1', 'max:3'],
            'tipo.*' => ['required', 'string', 'in:teoria,exercicio,revisao', 'distinct'],
        ];
    }
}
