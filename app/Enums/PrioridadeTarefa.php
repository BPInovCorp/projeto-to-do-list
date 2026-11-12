<?php

namespace App\Enums;

enum PrioridadeTarefa: string
{
    case Baixa = 'baixa';
    case Media = 'media';
    case Alta = 'alta';

    public function rotulo(): string
    {
        return match ($this) {
            self::Baixa => 'Baixa',
            self::Media => 'Média',
            self::Alta => 'Alta',
        };
    }
}