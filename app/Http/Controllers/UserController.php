<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD controller for users.
 */
class UserController extends Controller
{
    /**
     * List users (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'horario_semanal', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Show a single user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(
            $user->only(['id', 'name', 'email', 'role', 'horario_semanal', 'created_at', 'updated_at'])
        );
    }

    /**
     * Create a new user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['role'] = $data['role'] ?? 'user';
        $data['horario_semanal'] = $data['horario_semanal'] ?? [
            'domingo' => 0,
            'segunda' => 0,
            'terca' => 0,
            'quarta' => 0,
            'quinta' => 0,
            'sexta' => 0,
            'sabado' => 0,
        ];

        $user = User::create($data);

        return response()->json(
            $user->only(['id', 'name', 'email', 'role', 'horario_semanal', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Update an existing user.
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
            $user->only(['id', 'name', 'email', 'role', 'horario_semanal', 'created_at', 'updated_at'])
        );
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
