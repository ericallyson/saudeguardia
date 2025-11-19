<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\MetaResponseController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PacienteReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Webhooks\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/metas/responder/{token}', [MetaResponseController::class, 'show'])->name('metas.responder');
Route::post('/metas/responder/{token}', [MetaResponseController::class, 'store'])->name('metas.responder.store');

Route::get('/relatorios/pacientes/{paciente:uuid}', [PacienteReportController::class, 'show'])
    ->middleware('signed')
    ->name('pacientes.relatorios.publico');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::post('/home/register', [HomeController::class, 'register'])->name('home.register');
});

Route::post('/webhooks/whatsapp', WhatsappWebhookController::class)->name('webhooks.whatsapp');

Route::middleware(['auth', 'subscription.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('metas', MetaController::class)->except('show');
    Route::get('pacientes/{paciente:uuid}/dashboard', [PacienteController::class, 'dashboard'])
        ->name('pacientes.dashboard');
    Route::post('pacientes/{paciente:uuid}/cancelar-metas', [PacienteController::class, 'cancelarMetas'])
        ->name('pacientes.cancelar-metas');
    Route::post('pacientes/{paciente:uuid}/enviar-acompanhamento', [PacienteController::class, 'enviarAcompanhamento'])
        ->name('pacientes.enviar-acompanhamento');
    Route::resource('pacientes', PacienteController::class)
        ->except('show')
        ->scoped(['paciente' => 'uuid']);

    Route::get('configuracoes', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('configuracoes/instancia', [SettingsController::class, 'createInstance'])->name('settings.instance.create');
    Route::post('configuracoes/instancia/conectar', [SettingsController::class, 'connectInstance'])->name('settings.instance.connect');
    Route::post('configuracoes/instancia/desconectar', [SettingsController::class, 'disconnectInstance'])->name('settings.instance.disconnect');
    Route::post('configuracoes/instancia/excluir', [SettingsController::class, 'deleteInstance'])->name('settings.instance.delete');
    Route::get('configuracoes/instancia/status', [SettingsController::class, 'instanceStatus'])->name('settings.instance.status');
});
