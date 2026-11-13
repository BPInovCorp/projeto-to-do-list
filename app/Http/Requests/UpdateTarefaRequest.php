<?php

namespace App\Http\Requests;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use App\Models\Tarefa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tarefa = $this->route('tarefa');

        return $this->user() !== null && $this->user()->can('update', $tarefa);
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],
            'descricao' => ['sometimes', 'nullable', 'string'],
            'data_vencimento' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'prioridade' => ['sometimes', 'required', Rule::enum(PrioridadeTarefa::class)],
            'estado' => ['sometimes', 'required', Rule::enum(EstadoTarefa::class)],
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
            'estado.required' => 'A indicação do estado é obrigatória.',
            'estado.Illuminate\Validation\Rules\Enum' => 'O estado selecionado é inválido.',
        ];
    }
}