<?php

namespace App\Models;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarefa extends Model
{
    use HasFactory;

    protected $attributes = [
        'estado' => 'pendente',
        'prioridade' => 'media',
    ];

    protected $fillable = [
        'utilizador_id',
        'titulo',
        'descricao',
        'data_vencimento',
        'prioridade',
        'estado',
        'concluida_em',
    ];

    protected function casts(): array
    {
        return [
            'data_vencimento' => 'date:Y-m-d',
            'concluida_em' => 'datetime',
            'estado' => EstadoTarefa::class,
            'prioridade' => PrioridadeTarefa::class,
        ];
    }

    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(Utilizador::class, 'utilizador_id');
    }

    public function scopeFiltrarPorEstado(Builder $query, ?string $estado): Builder
    {
        if ($estado && in_array($estado, ['pendente', 'concluida'], true)) {
            return $query->where('estado', $estado);
        }

        return $query;
    }

    public function scopeFiltrarPorPrioridade(Builder $query, ?string $prioridade): Builder
    {
        if ($prioridade && in_array($prioridade, ['baixa', 'media', 'alta'], true)) {
            return $query->where('prioridade', $prioridade);
        }

        return $query;
    }

    public function scopeFiltrarPorPrazo(Builder $query, ?string $prazo): Builder
    {
        $hoje = today()->toDateString();

        return match ($prazo) {
            'hoje' => $query->where('data_vencimento', $hoje),
            'semana' => $query->whereBetween('data_vencimento', [$hoje, today()->addDays(7)->toDateString()]),
            'vencidas' => $query->where('estado', EstadoTarefa::Pendente)->where('data_vencimento', '<', $hoje),
            'sem_data' => $query->whereNull('data_vencimento'),
            default => $query,
        };
    }
}