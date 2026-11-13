<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\TarefaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
})->name('app.index');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::post('/registo', [AuthController::class, 'register'])->name('autenticacao.registar');
Route::post('/entrar', [AuthController::class, 'login'])->name('autenticacao.entrar');

Route::middleware('auth')->group(function () {
    Route::post('/sair', [AuthController::class, 'logout'])->name('autenticacao.sair');
    Route::get('/utilizador', [AuthController::class, 'user'])->name('autenticacao.utilizador');

    Route::get('/tarefas', [TarefaController::class, 'index'])->name('tarefas.index');
    Route::post('/tarefas', [TarefaController::class, 'store'])->name('tarefas.guardar');
    Route::get('/tarefas/{tarefa}', [TarefaController::class, 'show'])->name('tarefas.detalhes');
    Route::put('/tarefas/{tarefa}', [TarefaController::class, 'update'])->name('tarefas.atualizar');
    Route::patch('/tarefas/{tarefa}/alternar', [TarefaController::class, 'toggle'])->name('tarefas.alternar');
    Route::delete('/tarefas/{tarefa}', [TarefaController::class, 'destroy'])->name('tarefas.eliminar');
});