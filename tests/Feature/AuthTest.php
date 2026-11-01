<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un utilisateur peut se connecter avec les bons identifiants.
     */
    public function test_login_succeeds_with_valid_credentials(): void
    {
        $password = 'Password123!';

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'token_type',
                'user',
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => $user->getMorphClass(),
        ]);
    }

    /**
     * Une connexion avec un mauvais mot de passe échoue.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Identifiants invalides.',
            ]);
    }

    /**
     * Une route protégée refuse un utilisateur sans token.
     */
    public function test_protected_route_rejects_unauthenticated_user(): void
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertUnauthorized();
    }

    /**
     * La déconnexion révoque le token courant.
     */
    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->postJson('/api/auth/logout');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Déconnexion réussie.',
            ]);

        // Le même token ne doit plus permettre d'accéder au profil.
        $this->withToken($token)
            ->getJson('/api/auth/profile')
            ->assertUnauthorized();
    }

    /**
     * Un compte désactivé ne peut pas se connecter.
     */
    public function test_disabled_account_cannot_login(): void
    {
        $password = 'Password123!';

        User::factory()->create([
            'email' => 'disabled@example.com',
            'password' => Hash::make($password),
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'disabled@example.com',
            'password' => $password,
        ]);

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'Ce compte est désactivé.',
            ]);
    }
}
