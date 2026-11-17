<?php

namespace Tests\Unit;

use App\Enums\EstadoTarefa;
use App\Enums\PrioridadeTarefa;
use App\Models\Tarefa;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class TarefaUnitTest extends TestCase
{
    public function test_enum_estado_tarefa_devolve_rotulo_correto(): void
    {
        $this->assertEquals('Pendente', EstadoTarefa::Pendente->rotulo());
        $this->assertEquals('Concluída', EstadoTarefa::Concluida->rotulo());
    }

    public function test_enum_prioridade_tarefa_devolve_rotulo_correto(): void
    {
        $this->assertEquals('Baixa', PrioridadeTarefa::Baixa->rotulo());
        $this->assertEquals('Média', PrioridadeTarefa::Media->rotulo());
        $this->assertEquals('Alta', PrioridadeTarefa::Alta->rotulo());
    }

    public function test_tarefa_converte_atributos_para_casts_tipados(): void
    {
        $tarefa = new Tarefa([
            'data_vencimento' => '2026-11-20',
            'concluida_em' => '2026-11-15 10:30:00',
            'estado' => 'concluida',
            'prioridade' => 'alta',
        ]);

        $this->assertInstanceOf(CarbonInterface::class, $tarefa->data_vencimento);
        $this->assertInstanceOf(CarbonInterface::class, $tarefa->concluida_em);
        $this->assertInstanceOf(EstadoTarefa::class, $tarefa->estado);
        $this->assertInstanceOf(PrioridadeTarefa::class, $tarefa->prioridade);
    }

    public function test_tarefa_possui_relacao_belongs_to_utilizador(): void
    {
        $tarefa = new Tarefa();
        $relacao = $tarefa->utilizador();

        $this->assertInstanceOf(BelongsTo::class, $relacao);
        $this->assertEquals('utilizador_id', $relacao->getForeignKeyName());
    }

    public function test_tarefa_possui_atributos_padrao_em_memoria(): void
    {
        $tarefa = new Tarefa();

        $this->assertEquals(EstadoTarefa::Pendente, $tarefa->estado);
        $this->assertEquals(PrioridadeTarefa::Media, $tarefa->prioridade);
    }
}