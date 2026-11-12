<?php

namespace App\Enums;

enum EstadoTarefa: string
{
    case Pendente = 'pendente';
    case Concluida = 'concluida';

    public function rotulo(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Concluida => 'Concluída',
        };
    }
}