<?php

namespace App\Http\Controllers;

use App\Models\MetaMessage;
use App\Models\MetaResposta;
use App\Services\PacienteDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MetaResponseController extends Controller
{
    public function __construct(private readonly PacienteDashboardService $dashboardService)
    {
    }

    public function show(string $token): View
    {
        $message = MetaMessage::with(['meta', 'paciente', 'resposta'])
            ->where('token', $token)
            ->firstOrFail();

        $paciente = $message->paciente;
        $meta = $message->meta;
        $showChart = $message->status === 'respondido';
        $engajamento = $showChart ? $this->dashboardService->calcularEngajamento($paciente) : null;
        $metaCharts = $showChart
            ? $this->dashboardService->construirGraficosMetas($paciente, $meta)
            : collect();

        return view('metas.responder', [
            'metaMessage' => $message,
            'paciente' => $paciente,
            'meta' => $meta,
            'showChart' => $showChart,
            'engajamento' => $engajamento,
            'metaCharts' => $metaCharts,
        ]);
    }

    public function store(Request $request, string $token): View|RedirectResponse
    {
        $message = MetaMessage::with(['meta', 'paciente'])
            ->where('token', $token)
            ->firstOrFail();

        if ($message->status === 'respondido') {
            return redirect()->route('metas.responder', ['token' => $token]);
        }

        $meta = $message->meta;
        $valor = $this->validateValor($request, $meta->tipo);

        $agora = Carbon::now();

        DB::transaction(function () use ($message, $valor, $agora): void {
            MetaResposta::create([
                'meta_message_id' => $message->id,
                'paciente_id' => $message->paciente_id,
                'meta_id' => $message->meta_id,
                'valor' => $valor,
                'respondido_em' => $agora,
            ]);

            $message->update([
                'status' => 'respondido',
                'respondido_em' => $agora,
            ]);
        });

        $message->load(['paciente', 'meta']);
        $paciente = $message->paciente;

        $engajamento = $this->dashboardService->calcularEngajamento($paciente);
        $metaCharts = $this->dashboardService->construirGraficosMetas($paciente, $meta);

        return view('metas.responder', [
            'metaMessage' => $message,
            'paciente' => $paciente,
            'meta' => $meta,
            'showChart' => true,
            'engajamento' => $engajamento,
            'metaCharts' => $metaCharts,
        ]);
    }

    private function validateValor(Request $request, string $tipo): string
    {
        return match ($tipo) {
            'boolean' => $request->validate([
                'valor' => ['required', 'in:sim,nao'],
            ])['valor'],
            'integer' => $this->sanitizeDecimal($request->validate([
                'valor' => ['required', 'regex:/^-?\d+(?:[\.,]\d+)?$/'],
            ])['valor']),
            'blood_pressure' => $this->formatBloodPressure(
                $request->validate($this->bloodPressureRules())
            ),
            'scale' => (string) $request->validate([
                'valor' => ['required', 'integer', 'min:1', 'max:5'],
            ])['valor'],
            default => $request->validate([
                'valor' => ['required', 'string'],
            ])['valor'],
        };
    }

    private function sanitizeDecimal(string $valor): string
    {
        return str_replace(',', '.', trim($valor));
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\Rule>>
     */
    private function bloodPressureRules(): array
    {
        $opcoes = range(50, 220, 5);

        return [
            'valor_pas' => ['required', 'integer', Rule::in($opcoes)],
            'valor_pad' => ['required', 'integer', Rule::in($opcoes)],
        ];
    }

    /**
     * @param  array{valor_pas: int, valor_pad: int}  $valores
     */
    private function formatBloodPressure(array $valores): string
    {
        return sprintf('%dx%d', $valores['valor_pas'], $valores['valor_pad']);
    }
}
