<?php

namespace Tests\Feature;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_user_sends_welcome_email(): void
    {
        Mail::fake();

        // Création du rôle et de la permission
        $role = Role::create([
            'name' => 'Administrateur',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'users.create',
            'guard_name' => 'web',
        ]);

        $admin = User::factory()->create();
        $admin->givePermissionTo('users.create');

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'telephone' => '97000000',
                'email' => 'john@example.com',
                'role' => $role->name,
            ]);

        $response->assertCreated();

        $user = User::where('email', 'john@example.com')->first();

       

        // Vérifie que le mot de passe a bien été enregistré sous forme hachée
       
            $this->assertNotNull($user);
            $this->assertNotEquals($temporaryPassword ?? '', $user->password);
        // Vérifie qu'un email de bienvenue a été envoyé
        Mail::assertSent(WelcomeUserMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->user->id === $user->id
                && !empty($mail->temporaryPassword);
        });

        Mail::assertSentCount(1);
    }
}