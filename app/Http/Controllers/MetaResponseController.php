<?php

namespace App\Http\Controllers;

use App\Models\MetaMessage;
use App\Models\MetaResposta;
use App\Services\PacienteDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        return view('metas.responder', [
            'metaMessage' => $message,
            'paciente' => $paciente,
            'meta' => $meta,
            'showChart' => $showChart,
            'engajamento' => $engajamento,
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

        return view('metas.responder', [
            'metaMessage' => $message,
            'paciente' => $paciente,
            'meta' => $meta,
            'showChart' => true,
            'engajamento' => $engajamento,
        ]);
    }

    private function validateValor(Request $request, string $tipo): string
    {
        return match ($tipo) {
            'boolean' => $request->validate([
                'valor' => ['required', 'in:sim,nao'],
            ])['valor'],
            'integer' => (string) $request->validate([
                'valor' => ['required', 'integer'],
            ])['valor'],
            'scale' => (string) $request->validate([
                'valor' => ['required', 'integer', 'min:1', 'max:5'],
            ])['valor'],
            default => $request->validate([
                'valor' => ['required', 'string'],
            ])['valor'],
        };
    }

}
