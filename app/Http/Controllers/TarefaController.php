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
        $validated = $request->validate([
            'estado' => ['nullable', 'string', 'in:todas,pendente,concluida'],
            'prioridade' => ['nullable', 'string', 'in:todas,baixa,media,alta'],
            'prazo' => ['nullable', 'string', 'in:todos,hoje,semana,vencidas,sem_data'],
        ]);

        $baseQuery = $request->user()->tarefas();
        $hoje = today()->toDateString();

        $contadores = [
            'total' => (clone $baseQuery)->count(),
            'pendentes' => (clone $baseQuery)->where('estado', EstadoTarefa::Pendente)->count(),
            'concluidas' => (clone $baseQuery)->where('estado', EstadoTarefa::Concluida)->count(),
            'vencidas' => (clone $baseQuery)->where('estado', EstadoTarefa::Pendente)->where('data_vencimento', '<', $hoje)->count(),
        ];

        $tarefas = (clone $baseQuery)
            ->filtrarPorEstado($validated['estado'] ?? null)
            ->filtrarPorPrioridade($validated['prioridade'] ?? null)
            ->filtrarPorPrazo($validated['prazo'] ?? null)
            ->latest()
            ->get();

        return response()->json([
            'tarefas' => $tarefas,
            'contadores' => $contadores,
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