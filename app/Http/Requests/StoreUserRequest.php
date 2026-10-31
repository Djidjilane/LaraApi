<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lastname' => ['required', 'string', 'max:100'],
            'firstname' => ['required', 'string', 'max:100'],

            'telephone' => ['nullable', 'string', 'max:20'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->whereIn('name', [
                        'Administrateur',
                        'Directeur des Études',
                        'Secrétaire',
                    ]),
            ],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'lastname.required' => 'Le nom est obligatoire.',
            'firstname.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L’adresse email est obligatoire.',
            'email.email' => 'L’adresse email est invalide.',
            'email.unique' => 'Cette adresse email existe déjà.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.exists' => 'Le rôle sélectionné est invalide.',
        ];
    }
}
