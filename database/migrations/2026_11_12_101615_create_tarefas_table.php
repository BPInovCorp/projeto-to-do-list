<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_id')->constrained('utilizadores')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->string('prioridade', 20)->default('media');
            $table->string('estado', 20)->default('pendente');
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();

            $table->index(['utilizador_id', 'estado']);
            $table->index(['utilizador_id', 'prioridade']);
            $table->index(['utilizador_id', 'data_vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
