<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\MetaResponseController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Webhooks\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/metas/responder/{token}', [MetaResponseController::class, 'show'])->name('metas.responder');
Route::post('/metas/responder/{token}', [MetaResponseController::class, 'store'])->name('metas.responder.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/webhooks/whatsapp', WhatsappWebhookController::class)->name('webhooks.whatsapp');

Route::middleware(['auth', 'subscription.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('metas', MetaController::class)->except('show');
    Route::get('pacientes/{paciente}/dashboard', [PacienteController::class, 'dashboard'])
        ->name('pacientes.dashboard');
    Route::resource('pacientes', PacienteController::class)->except('show');

    Route::get('configuracoes', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('configuracoes/instancia', [SettingsController::class, 'createInstance'])->name('settings.instance.create');
    Route::post('configuracoes/instancia/conectar', [SettingsController::class, 'connectInstance'])->name('settings.instance.connect');
    Route::post('configuracoes/instancia/desconectar', [SettingsController::class, 'disconnectInstance'])->name('settings.instance.disconnect');
    Route::get('configuracoes/instancia/status', [SettingsController::class, 'instanceStatus'])->name('settings.instance.status');
});
