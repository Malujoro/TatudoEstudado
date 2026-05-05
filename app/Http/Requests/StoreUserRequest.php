<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de usuário.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Autoriza a execução do request.
     *
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
            'horas_por_dia' => ['sometimes', 'numeric', 'min:0'],
            'role' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
