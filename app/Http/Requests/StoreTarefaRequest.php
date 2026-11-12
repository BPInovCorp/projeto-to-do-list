<?php

namespace App\Http\Requests;

use App\Enums\PrioridadeTarefa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data_vencimento' => ['nullable', 'date_format:Y-m-d'],
            'prioridade' => ['required', Rule::enum(PrioridadeTarefa::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'O título da tarefa é de preenchimento obrigatório.',
            'titulo.string' => 'O título deve ser um texto válido.',
            'titulo.max' => 'O título não pode ter mais de 255 carateres.',
            'descricao.string' => 'A descrição deve ser um texto válido.',
            'data_vencimento.date_format' => 'A data limite deve respeitar o formato AAAA-MM-DD.',
            'prioridade.required' => 'A indicação da prioridade é obrigatória.',
            'prioridade.Illuminate\Validation\Rules\Enum' => 'A prioridade selecionada é inválida.',
        ];
    }
}