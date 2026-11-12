<?php

namespace Database\Factories;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use App\Models\Tarefa;
use App\Models\Utilizador;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarefaFactory extends Factory
{
    protected $model = Tarefa::class;

    public function definition(): array
    {
        return [
            'utilizador_id' => Utilizador::factory(),
            'titulo' => fake()->sentence(4),
            'descricao' => fake()->paragraph(2),
            'data_vencimento' => fake()->optional()->dateTimeBetween('+1 day', '+1 month')?->format('Y-m-d'),
            'prioridade' => fake()->randomElement([
                PrioridadeTarefa::Baixa,
                PrioridadeTarefa::Media,
                PrioridadeTarefa::Alta,
            ]),
            'estado' => EstadoTarefa::Pendente,
            'concluida_em' => null,
        ];
    }

    public function concluida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoTarefa::Concluida,
            'concluida_em' => now(),
        ]);
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoTarefa::Pendente,
            'concluida_em' => null,
        ]);
    }

    public function prioridadeAlta(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridade' => PrioridadeTarefa::Alta,
        ]);
    }

    public function prioridadeMedia(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridade' => PrioridadeTarefa::Media,
        ]);
    }

    public function prioridadeBaixa(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridade' => PrioridadeTarefa::Baixa,
        ]);
    }

    public function semVencimento(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_vencimento' => null,
        ]);
    }

    public function vencida(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_vencimento' => now()->subDays(2)->format('Y-m-d'),
        ]);
    }
}