<?php

namespace App\Policies;

use App\Models\Tarefa;
use App\Models\Utilizador;

class TarefaPolicy
{
    public function viewAny(Utilizador $utilizador): bool
    {
        return true;
    }

    public function view(Utilizador $utilizador, Tarefa $tarefa): bool
    {
        return $utilizador->id === $tarefa->utilizador_id;
    }

    public function create(Utilizador $utilizador): bool
    {
        return true;
    }

    public function update(Utilizador $utilizador, Tarefa $tarefa): bool
    {
        return $utilizador->id === $tarefa->utilizador_id;
    }

    public function delete(Utilizador $utilizador, Tarefa $tarefa): bool
    {
        return $utilizador->id === $tarefa->utilizador_id;
    }
}