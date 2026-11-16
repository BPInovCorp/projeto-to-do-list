<?php

namespace Tests\Feature;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use App\Models\Tarefa;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaFiltrosTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtra_tarefas_por_estado(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Tarefa Pendente',
            'estado' => EstadoTarefa::Pendente,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Tarefa Concluída',
            'estado' => EstadoTarefa::Concluida,
            'concluida_em' => now(),
        ]);

        $responsePendentes = $this->actingAs($utilizador)
            ->getJson('/tarefas?estado=pendente');

        $responsePendentes->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Tarefa Pendente');

        $responseConcluidas = $this->actingAs($utilizador)
            ->getJson('/tarefas?estado=concluida');

        $responseConcluidas->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Tarefa Concluída');
    }

    public function test_filtra_tarefas_por_prioridade(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Tarefa Baixa',
            'prioridade' => PrioridadeTarefa::Baixa,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Tarefa Alta',
            'prioridade' => PrioridadeTarefa::Alta,
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson('/tarefas?prioridade=alta');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Tarefa Alta');
    }

    public function test_filtra_tarefas_por_prazo(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $hoje = today()->toDateString();
        $amanha = today()->addDay()->toDateString();
        $passado = today()->subDays(3)->toDateString();

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Para Hoje',
            'data_vencimento' => $hoje,
            'estado' => EstadoTarefa::Pendente,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Para Esta Semana',
            'data_vencimento' => $amanha,
            'estado' => EstadoTarefa::Pendente,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Atrasada',
            'data_vencimento' => $passado,
            'estado' => EstadoTarefa::Pendente,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Sem Data',
            'data_vencimento' => null,
            'estado' => EstadoTarefa::Pendente,
        ]);

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=hoje')
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Para Hoje');

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=semana')
            ->assertJsonCount(2, 'tarefas');

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=vencidas')
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Atrasada');

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=sem_data')
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Sem Data');
    }

    public function test_combina_multiplos_filtros_simultaneamente(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Alvo Esperado',
            'prioridade' => PrioridadeTarefa::Alta,
            'estado' => EstadoTarefa::Pendente,
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Alta mas Concluída',
            'prioridade' => PrioridadeTarefa::Alta,
            'estado' => EstadoTarefa::Concluida,
            'concluida_em' => now(),
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Pendente mas Média',
            'prioridade' => PrioridadeTarefa::Media,
            'estado' => EstadoTarefa::Pendente,
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson('/tarefas?prioridade=alta&estado=pendente');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.titulo', 'Alvo Esperado');
    }

    public function test_calcula_contadores_de_resumo_independentes_dos_filtros(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Pendente,
            'prioridade' => PrioridadeTarefa::Alta,
            'data_vencimento' => today()->subDays(2)->toDateString(),
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Pendente,
            'prioridade' => PrioridadeTarefa::Baixa,
            'data_vencimento' => today()->addDays(5)->toDateString(),
        ]);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Concluida,
            'prioridade' => PrioridadeTarefa::Media,
            'concluida_em' => now(),
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson('/tarefas?estado=concluida');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('contadores.total', 3)
            ->assertJsonPath('contadores.pendentes', 2)
            ->assertJsonPath('contadores.concluidas', 1)
            ->assertJsonPath('contadores.vencidas', 1);
    }

    public function test_tarefa_concluida_com_data_ultrapassada_nao_consta_em_vencidas(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Concluída no Passado',
            'estado' => EstadoTarefa::Concluida,
            'data_vencimento' => today()->subDays(5)->toDateString(),
            'concluida_em' => today()->subDays(6),
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=vencidas');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'tarefas')
            ->assertJsonPath('contadores.vencidas', 0);
    }

    public function test_rejeita_parametros_invalidos_com_erro_422(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $this->actingAs($utilizador)
            ->getJson('/tarefas?estado=inexistente')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prioridade=urgente')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['prioridade']);

        $this->actingAs($utilizador)
            ->getJson('/tarefas?prazo=invalido')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['prazo']);
    }

    public function test_combinacao_de_filtros_sem_resultados_devolve_colecao_vazia(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'prioridade' => PrioridadeTarefa::Baixa,
            'estado' => EstadoTarefa::Pendente,
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson('/tarefas?prioridade=alta&estado=concluida');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'tarefas')
            ->assertJsonPath('tarefas', []);
    }

    public function test_filtros_e_contadores_respeitam_isolamento_por_utilizador(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
            'titulo' => 'Tarefa Oculta B',
            'estado' => EstadoTarefa::Pendente,
            'data_vencimento' => today()->subDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($utilizadorA)
            ->getJson('/tarefas');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'tarefas')
            ->assertJsonPath('contadores.total', 0)
            ->assertJsonPath('contadores.pendentes', 0)
            ->assertJsonPath('contadores.vencidas', 0);
    }
}