<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
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
});