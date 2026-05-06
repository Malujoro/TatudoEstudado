<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de usuários.
 *
 * Este controller é pensado para consumo via front (posteriormente) e retorna JSON.
 * Rotas registradas como `apiResource` sob prefixo `/api` e middleware `auth`.
 */
class UserController extends Controller
{
    /**
     * Lista usuários com paginação.
     *
     * Query params suportados:
     * - `per_page` (int): quantidade por página (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $users = User::query()
            ->select(['id', 'name', 'email', 'horas_por_dia', 'role', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Exibe um usuário específico.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(
            $user->only(['id', 'name', 'email', 'horas_por_dia', 'role', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria um novo usuário.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['horas_por_dia'] = $data['horas_por_dia'] ?? 0;
        $data['role'] = $data['role'] ?? 'user';

        $user = User::create($data);

        return response()->json(
            $user->only(['id', 'name', 'email', 'horas_por_dia', 'role', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza um usuário existente.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (! array_key_exists('password', $data) || $data['password'] === null || $data['password'] === '') {
            unset($data['password']);
        }

        $user->fill($data);
        $user->save();

        return response()->json(
            $user->only(['id', 'name', 'email', 'horas_por_dia', 'role', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove um usuário.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
