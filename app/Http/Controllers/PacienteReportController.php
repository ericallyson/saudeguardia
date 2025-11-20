<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Services\PacienteDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteReportController extends Controller
{
    public function __construct(private readonly PacienteDashboardService $dashboardService)
    {
    }

    public function show(Request $request, Paciente $paciente): View
    {
        $paciente->load('metas');

        $engajamento = $this->dashboardService->calcularEngajamento($paciente);
        $andamento = $this->dashboardService->calcularAndamentoTratamento($paciente);
        $metaCharts = $this->dashboardService->construirGraficosMetas($paciente);

        return view('pacientes.report', [
            'paciente' => $paciente,
            'engajamento' => $engajamento,
            'andamento' => $andamento,
            'metaCharts' => $metaCharts,
        ]);
    }
}
