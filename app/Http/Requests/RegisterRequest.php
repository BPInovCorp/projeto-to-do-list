<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:utilizadores,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é de preenchimento obrigatório.',
            'nome.string' => 'O nome indicado deve ser uma cadeia de carateres.',
            'nome.max' => 'O nome não pode exceder os 255 carateres.',
            'email.required' => 'O campo email é de preenchimento obrigatório.',
            'email.string' => 'O email deve ser um texto válido.',
            'email.email' => 'Introduza um endereço de email válido.',
            'email.max' => 'O endereço de email não pode ter mais de 255 carateres.',
            'email.unique' => 'Este endereço de email já se encontra registado no sistema.',
            'password.required' => 'A palavra-passe é de preenchimento obrigatório.',
            'password.string' => 'A palavra-passe deve ser um texto válido.',
            'password.min' => 'A palavra-passe deve conter pelo menos 8 carateres.',
            'password.confirmed' => 'A confirmação da palavra-passe não coincide.',
        ];
    }
}