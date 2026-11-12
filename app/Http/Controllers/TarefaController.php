<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarefaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tarefas = $request->user()->tarefas()->latest()->get();

        return response()->json([
            'tarefas' => $tarefas,
        ]);
    }

    public function store(StoreTarefaRequest $request): JsonResponse
    {
        $tarefa = $request->user()->tarefas()->create($request->validated());

        return response()->json([
            'status' => 'sucesso',
            'tarefa' => $tarefa,
        ], 201);
    }
}