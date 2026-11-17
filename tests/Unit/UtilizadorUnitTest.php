<?php

namespace Tests\Unit;

use App\Models\Utilizador;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UtilizadorUnitTest extends TestCase
{
    public function test_utilizador_possui_relacao_has_many_tarefas(): void
    {
        $utilizador = new Utilizador();
        $relacao = $utilizador->tarefas();

        $this->assertInstanceOf(HasMany::class, $relacao);
        $this->assertEquals('utilizador_id', $relacao->getForeignKeyName());
    }

    public function test_utilizador_oculta_atributos_sensiveis_na_serializacao(): void
    {
        $utilizador = new Utilizador([
            'nome' => 'Carlos Sousa',
            'email' => 'carlos@inovcorp.pt',
            'password' => 'segredo123',
        ]);
        $utilizador->remember_token = 'token_segredo';

        $serializado = $utilizador->toArray();

        $this->assertArrayNotHasKey('password', $serializado);
        $this->assertArrayNotHasKey('remember_token', $serializado);
        $this->assertArrayHasKey('nome', $serializado);
        $this->assertArrayHasKey('email', $serializado);
    }

    public function test_utilizador_aplica_hash_automatico_na_palavra_passe(): void
    {
        $utilizador = new Utilizador([
            'password' => 'palavrapasse123',
        ]);

        $this->assertNotEquals('palavrapasse123', $utilizador->password);
        $this->assertTrue(Hash::check('palavrapasse123', $utilizador->password));
    }
}