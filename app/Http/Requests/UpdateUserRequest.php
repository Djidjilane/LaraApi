<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'lastname' => ['sometimes', 'required', 'string', 'max:100'],
            'firstname' => ['sometimes', 'required', 'string', 'max:100'],

            'telephone' => ['sometimes', 'nullable', 'string', 'max:20'],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'sometimes',
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
}
