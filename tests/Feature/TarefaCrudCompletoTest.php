<?php

namespace Tests\Feature;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use App\Models\Tarefa;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaCrudCompletoTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilizador_pode_consultar_detalhes_da_sua_tarefa(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Rever relatório financeiro',
            'descricao' => 'Analisar despesas do mês corrente.',
        ]);

        $response = $this->actingAs($utilizador)
            ->getJson("/tarefas/{$tarefa->id}");

        $response->assertStatus(200)
            ->assertJsonPath('tarefa.id', $tarefa->id)
            ->assertJsonPath('tarefa.titulo', 'Rever relatório financeiro')
            ->assertJsonPath('tarefa.descricao', 'Analisar despesas do mês corrente.');
    }

    public function test_impede_consulta_de_tarefa_de_outro_utilizador(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        $tarefaB = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
            'titulo' => 'Tarefa confidencial B',
        ]);

        $response = $this->actingAs($utilizadorA)
            ->getJson("/tarefas/{$tarefaB->id}");

        $response->assertStatus(403);
    }

    public function test_utilizador_pode_editar_sua_tarefa(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Título Antigo',
            'prioridade' => 'baixa',
        ]);

        $response = $this->actingAs($utilizador)
            ->putJson("/tarefas/{$tarefa->id}", [
                'titulo' => 'Título Atualizado',
                'descricao' => 'Nova descrição adicionada.',
                'prioridade' => 'alta',
                'data_vencimento' => '2026-12-01',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('tarefa.titulo', 'Título Atualizado')
            ->assertJsonPath('tarefa.prioridade', 'alta')
            ->assertJsonPath('tarefa.data_vencimento', '2026-12-01');

        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefa->id,
            'titulo' => 'Título Atualizado',
            'prioridade' => 'alta',
        ]);
    }

    public function test_impede_edicao_de_tarefa_de_outro_utilizador(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        $tarefaB = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
            'titulo' => 'Inalterável por terceiros',
        ]);

        $response = $this->actingAs($utilizadorA)
            ->putJson("/tarefas/{$tarefaB->id}", [
                'titulo' => 'Tentativa de Fraude',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefaB->id,
            'titulo' => 'Inalterável por terceiros',
        ]);
    }

    public function test_utilizador_pode_alternar_estado_da_tarefa(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Pendente,
            'concluida_em' => null,
        ]);

        $response = $this->actingAs($utilizador)
            ->patchJson("/tarefas/{$tarefa->id}/alternar");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('tarefa.estado', 'concluida');

        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefa->id,
            'estado' => 'concluida',
        ]);
        $this->assertNotNull($tarefa->fresh()->concluida_em);

        $responseVolta = $this->actingAs($utilizador)
            ->patchJson("/tarefas/{$tarefa->id}/alternar");

        $responseVolta->assertStatus(200)
            ->assertJsonPath('tarefa.estado', 'pendente');

        $this->assertNull($tarefa->fresh()->concluida_em);
    }

    public function test_utilizador_pode_eliminar_sua_tarefa(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
        ]);

        $response = $this->actingAs($utilizador)
            ->deleteJson("/tarefas/{$tarefa->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso');

        $this->assertDatabaseMissing('tarefas', [
            'id' => $tarefa->id,
        ]);
    }

    public function test_impede_eliminacao_de_tarefa_de_outro_utilizador(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        $tarefaB = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
        ]);

        $response = $this->actingAs($utilizadorA)
            ->deleteJson("/tarefas/{$tarefaB->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefaB->id,
        ]);
    }

    public function test_impede_alternar_tarefa_de_outro_utilizador(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        $tarefaB = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
            'estado' => EstadoTarefa::Pendente,
        ]);

        $response = $this->actingAs($utilizadorA)
            ->patchJson("/tarefas/{$tarefaB->id}/alternar");

        $response->assertStatus(403);
    }

    public function test_preserva_data_conclusao_quando_tarefa_concluida_mantem_estado(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $dataOriginal = now()->subDays(3)->startOfSecond();

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Concluida,
            'concluida_em' => $dataOriginal,
        ]);

        $response = $this->actingAs($utilizador)
            ->putJson("/tarefas/{$tarefa->id}", [
                'titulo' => 'Alterado apenas o título',
                'estado' => 'concluida',
            ]);

        $response->assertStatus(200);

        $this->assertEquals(
            $dataOriginal->toIso8601String(),
            $tarefa->fresh()->concluida_em->toIso8601String()
        );
    }

    public function test_anula_data_conclusao_quando_tarefa_regressa_a_pendente(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $tarefa = Tarefa::factory()->create([
            'utilizador_id' => $utilizador->id,
            'estado' => EstadoTarefa::Concluida,
            'concluida_em' => now(),
        ]);

        $response = $this->actingAs($utilizador)
            ->putJson("/tarefas/{$tarefa->id}", [
                'estado' => 'pendente',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('tarefa.estado', 'pendente');

        $this->assertNull($tarefa->fresh()->concluida_em);
    }

    public function test_impede_operacoes_sem_autenticacao(): void
    {
        $tarefa = Tarefa::factory()->create();

        $this->getJson("/tarefas/{$tarefa->id}")->assertStatus(401);
        $this->putJson("/tarefas/{$tarefa->id}", ['titulo' => 'Teste'])->assertStatus(401);
        $this->patchJson("/tarefas/{$tarefa->id}/alternar")->assertStatus(401);
        $this->deleteJson("/tarefas/{$tarefa->id}")->assertStatus(401);
    }
}