<?php

namespace Tests\Feature;

use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilizador_pode_registar_com_sucesso(): void
    {
        $response = $this->postJson('/registo', [
            'nome' => 'Rui Silva',
            'email' => 'rui.silva@inovcorp.pt',
            'password' => 'palavrapasse123',
            'password_confirmation' => 'palavrapasse123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('utilizador.email', 'rui.silva@inovcorp.pt');

        $this->assertDatabaseHas('utilizadores', [
            'email' => 'rui.silva@inovcorp.pt',
            'nome' => 'Rui Silva',
        ]);

        $this->assertAuthenticated();
    }

    public function test_impede_registo_com_email_duplicado(): void
    {
        Utilizador::create([
            'nome' => 'Existente',
            'email' => 'duplicado@inovcorp.pt',
            'password' => 'palavrapasse123',
        ]);

        $response = $this->postJson('/registo', [
            'nome' => 'Outro Nome',
            'email' => 'duplicado@inovcorp.pt',
            'password' => 'palavrapasse123',
            'password_confirmation' => 'palavrapasse123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_impede_registo_com_dados_invalidos(): void
    {
        $response = $this->postJson('/registo', [
            'nome' => '',
            'email' => 'email-invalido',
            'password' => 'curta',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'email', 'password']);
    }

    public function test_utilizador_pode_iniciar_sessao_com_credenciais_validas(): void
    {
        Utilizador::create([
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@inovcorp.pt',
            'password' => 'segredo123',
        ]);

        $response = $this->postJson('/entrar', [
            'email' => 'maria.santos@inovcorp.pt',
            'password' => 'segredo123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('utilizador.email', 'maria.santos@inovcorp.pt');

        $this->assertAuthenticated();
    }

    public function test_impede_inicio_sessao_com_credenciais_invalidas(): void
    {
        Utilizador::create([
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@inovcorp.pt',
            'password' => 'segredo123',
        ]);

        $response = $this->postJson('/entrar', [
            'email' => 'maria.santos@inovcorp.pt',
            'password' => 'palavraerrada',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_utilizador_pode_terminar_sessao(): void
    {
        $utilizador = Utilizador::create([
            'nome' => 'Carlos Sousa',
            'email' => 'carlos.sousa@inovcorp.pt',
            'password' => 'segredo123',
        ]);

        $this->actingAs($utilizador);

        $response = $this->postJson('/sair');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso');

        $this->assertGuest();
    }

    public function test_impede_consulta_de_utilizador_sem_autenticacao(): void
    {
        $response = $this->getJson('/utilizador');

        $response->assertStatus(401);
    }

    public function test_permite_consultar_dados_do_utilizador_autenticado(): void
    {
        $utilizador = Utilizador::create([
            'nome' => 'Ana Dias',
            'email' => 'ana.dias@inovcorp.pt',
            'password' => 'segredo123',
        ]);

        $this->actingAs($utilizador);

        $response = $this->getJson('/utilizador');

        $response->assertStatus(200)
            ->assertJsonPath('utilizador.id', $utilizador->id)
            ->assertJsonPath('utilizador.nome', 'Ana Dias');
    }
}