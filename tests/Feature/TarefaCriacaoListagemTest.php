<?php

namespace Tests\Feature;

use App\Models\Tarefa;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaCriacaoListagemTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilizador_autenticado_pode_criar_tarefa_com_sucesso(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $dados = [
            'titulo' => 'Preparar apresentação executiva',
            'descricao' => 'Concluir os slides de estratégia para o próximo trimestre.',
            'data_vencimento' => '2026-11-20',
            'prioridade' => 'alta',
        ];

        $response = $this->actingAs($utilizador)
            ->postJson('/tarefas', $dados);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('tarefa.titulo', 'Preparar apresentação executiva')
            ->assertJsonPath('tarefa.prioridade', 'alta')
            ->assertJsonPath('tarefa.estado', 'pendente');

        $this->assertDatabaseHas('tarefas', [
            'utilizador_id' => $utilizador->id,
            'titulo' => 'Preparar apresentação executiva',
            'prioridade' => 'alta',
            'estado' => 'pendente',
        ]);
    }

    public function test_impede_criacao_de_tarefa_com_dados_invalidos(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $response = $this->actingAs($utilizador)
            ->postJson('/tarefas', [
                'titulo' => '',
                'prioridade' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['titulo', 'prioridade']);
    }

    public function test_utilizador_apenas_visualiza_as_suas_proprias_tarefas(): void
    {
        $utilizadorA = Utilizador::factory()->create();
        $utilizadorB = Utilizador::factory()->create();
        assert($utilizadorA instanceof Utilizador);
        assert($utilizadorB instanceof Utilizador);

        $tarefaA = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorA->id,
            'titulo' => 'Tarefa do Utilizador A',
        ]);

        $tarefaB = Tarefa::factory()->create([
            'utilizador_id' => $utilizadorB->id,
            'titulo' => 'Tarefa do Utilizador B',
        ]);

        $response = $this->actingAs($utilizadorA)
            ->getJson('/tarefas');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'tarefas')
            ->assertJsonPath('tarefas.0.id', $tarefaA->id)
            ->assertJsonPath('tarefas.0.titulo', 'Tarefa do Utilizador A');

        $tarefasIds = collect($response->json('tarefas'))->pluck('id');
        $this->assertNotContains($tarefaB->id, $tarefasIds);
    }

    public function test_impede_acesso_a_tarefas_sem_autenticacao(): void
    {
        $responseGet = $this->getJson('/tarefas');
        $responseGet->assertStatus(401);

        $responsePost = $this->postJson('/tarefas', [
            'titulo' => 'Tarefa Proibida',
            'prioridade' => 'media',
        ]);
        $responsePost->assertStatus(401);
    }

    public function test_tarefa_criada_fica_associada_ao_utilizador_autenticado(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $response = $this->actingAs($utilizador)
            ->postJson('/tarefas', [
                'titulo' => 'Verificar integridade referencial',
                'prioridade' => 'baixa',
            ]);

        $response->assertStatus(201);

        $tarefaId = $response->json('tarefa.id');
        $tarefaCriada = Tarefa::findOrFail($tarefaId);

        $this->assertEquals($utilizador->id, $tarefaCriada->utilizador_id);
        $this->assertTrue($utilizador->tarefas->contains($tarefaCriada));
    }

    public function test_prioridade_invalida_nao_permite_criar_tarefa(): void
    {
        $utilizador = Utilizador::factory()->create();
        assert($utilizador instanceof Utilizador);

        $response = $this->actingAs($utilizador)
            ->postJson('/tarefas', [
                'titulo' => 'Prioridade Inválida Teste',
                'prioridade' => 'urgente',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prioridade']);
    }
}