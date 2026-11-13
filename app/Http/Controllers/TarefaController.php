<?php

namespace App\Http\Controllers;

use App\Enums\EstadoTarefa;
use App\Http\Requests\StoreTarefaRequest;
use App\Http\Requests\UpdateTarefaRequest;
use App\Models\Tarefa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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

    public function show(Request $request, Tarefa $tarefa): JsonResponse
    {
        Gate::authorize('view', $tarefa);

        return response()->json([
            'tarefa' => $tarefa,
        ]);
    }

    public function update(UpdateTarefaRequest $request, Tarefa $tarefa): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('estado', $validated)) {
            $novoEstado = $validated['estado'] instanceof EstadoTarefa ? $validated['estado']->value : $validated['estado'];
            $estadoAtual = $tarefa->estado instanceof EstadoTarefa ? $tarefa->estado->value : (string) $tarefa->estado;

            if ($novoEstado === 'concluida' && $estadoAtual !== 'concluida') {
                $validated['concluida_em'] = now();
            } elseif ($novoEstado === 'pendente') {
                $validated['concluida_em'] = null;
            }
        }

        $tarefa->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'tarefa' => $tarefa->fresh(),
        ]);
    }

    public function toggle(Request $request, Tarefa $tarefa): JsonResponse
    {
        Gate::authorize('update', $tarefa);

        return DB::transaction(function () use ($tarefa) {
            $tarefaBloqueada = Tarefa::where('id', $tarefa->id)->lockForUpdate()->firstOrFail();

            if ($tarefaBloqueada->estado === EstadoTarefa::Concluida) {
                $tarefaBloqueada->estado = EstadoTarefa::Pendente;
                $tarefaBloqueada->concluida_em = null;
            } else {
                $tarefaBloqueada->estado = EstadoTarefa::Concluida;
                $tarefaBloqueada->concluida_em = now();
            }

            $tarefaBloqueada->save();

            return response()->json([
                'status' => 'sucesso',
                'tarefa' => $tarefaBloqueada,
            ]);
        });
    }

    public function destroy(Request $request, Tarefa $tarefa): JsonResponse
    {
        Gate::authorize('delete', $tarefa);

        $tarefa->delete();

        return response()->json([
            'status' => 'sucesso',
        ]);
    }
}