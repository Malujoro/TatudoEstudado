<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validação para atualização de usuário.
 */
class UpdateUserRequest extends FormRequest
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
     * `password` é opcional na atualização.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => ['nullable', 'string', 'confirmed', 'min:6'],
            'horas_por_dia' => ['sometimes', 'numeric', 'min:0'],
            'role' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
