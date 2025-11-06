<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\MetaResponseController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/metas/responder/{token}', [MetaResponseController::class, 'show'])->name('metas.responder');
Route::post('/metas/responder/{token}', [MetaResponseController::class, 'store'])->name('metas.responder.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('metas', MetaController::class)->except('show');
    Route::get('pacientes/{paciente}/dashboard', [PacienteController::class, 'dashboard'])
        ->name('pacientes.dashboard');
    Route::resource('pacientes', PacienteController::class)->except('show');
});
