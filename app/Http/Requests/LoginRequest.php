<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é de preenchimento obrigatório.',
            'email.string' => 'O email deve ser um texto válido.',
            'email.email' => 'Introduza um endereço de email válido.',
            'password.required' => 'A palavra-passe é de preenchimento obrigatório.',
            'password.string' => 'A palavra-passe deve ser um texto válido.',
        ];
    }
}