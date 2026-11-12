<?php

namespace App\Models;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
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
}