<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'lastname'  => $request->lastname,
            'firstname' => $request->firstname,
            'telephone' => $request->telephone,
            'email'     => $request->email,
            'password'  => $request->password, // hashé automatiquement grâce au cast 'hashed'
        ]);

        // Attribuer le rôle par défaut
        $user->assignRole('admin');

        // Journaliser l'inscription
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Inscription d\'un nouvel utilisateur');

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user' => [
                'id' => $user->id,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'telephone' => $user->telephone,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.',
            ], 401);
        }

        // Supprimer les anciens tokens (optionnel)
        $user->tokens()->delete();

        // Créer un nouveau token Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        // Journaliser la connexion
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Connexion');

        return response()->json([
            'message' => 'Connexion réussie.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'telephone' => $user->telephone,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
        ], 200);
    }
    public function logout(Request $request)
{
    $user = $request->user();

    // Journaliser la déconnexion
    activity()
        ->causedBy($user)
        ->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log('Déconnexion');

    // Supprimer le token utilisé pour cette connexion
    $user->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Déconnexion réussie.'
    ], 200);
}
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return response()->json([
        'message' => __($status)
    ]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }
    );

    return response()->json([
        'message' => __($status),
    ]);
}
 public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }


}