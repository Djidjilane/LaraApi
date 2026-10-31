<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')
            ->latest()
            ->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Créer un utilisateur.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = DB::transaction(function () use ($request) {

            $user = User::create([
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'password' => $request->password,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $user->assignRole($request->role);

            return $user;
        });

        return (new UserResource($user->load('roles')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Afficher un utilisateur.
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user->load('roles'));
    }

    /**
     * Modifier un utilisateur.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): UserResource {

        $this->authorize('update', $user);

        $data = $request->validated();

        $role = $data['role'] ?? null;

        unset($data['role']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        DB::transaction(function () use ($user, $data, $role) {

            $user->update($data);

            if ($role !== null) {
                $user->syncRoles([$role]);
            }
        });

        return new UserResource($user->fresh()->load('roles'));
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ], 200);
    }
}
