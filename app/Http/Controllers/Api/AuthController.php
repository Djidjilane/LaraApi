<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Connexion API.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 401);
        }

        // À adapter au champ réel de la base de données.
        if (isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'message' => 'Ce compte est désactivé.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    /**
     * Déconnexion : révocation du token courant.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ], 200);
    }

    /**
     * Profil de l'utilisateur connecté.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }
}
