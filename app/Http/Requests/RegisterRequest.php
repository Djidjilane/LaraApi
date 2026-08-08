<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
    return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'lastname' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'telephone' => 'required|string|max:20|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
        ];
    }
    public function messages(): array
{
    return [
        'email.required' => "L'adresse e-mail est obligatoire.",
        'email.unique' => "Cette adresse e-mail est déjà utilisée.",
        'password.confirmed' => "La confirmation du mot de passe est incorrecte.",
    ];
}
}
