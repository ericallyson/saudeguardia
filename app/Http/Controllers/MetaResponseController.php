<?php

namespace App\Http\Controllers;

use App\Models\MetaMessage;
use App\Models\MetaResposta;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MetaResponseController extends Controller
{
    public function show(string $token): View
    {
        $message = MetaMessage::with(['meta', 'paciente', 'resposta'])
            ->where('token', $token)
            ->firstOrFail();

        $paciente = $message->paciente;
        $meta = $message->meta;
        $showChart = $message->status === 'respondido';
        $engajamento = $showChart ? $this->calcularEngajamento($paciente) : null;

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

        $engajamento = $this->calcularEngajamento($paciente);

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

    private function calcularEngajamento(Paciente $paciente): array
    {
        $totalInteracoes = $paciente->metaMessages()->count();
        $interacoesConcluidas = $paciente->metaRespostas()->count();
        $previstasAteHoje = $paciente->metaMessages()
            ->where('data_envio', '<=', Carbon::now())
            ->count();

        $percentualTotal = $totalInteracoes > 0
            ? round(($interacoesConcluidas / $totalInteracoes) * 100, 2)
            : 0;

        $percentualPrevisto = $previstasAteHoje > 0
            ? round(min(100, ($interacoesConcluidas / $previstasAteHoje) * 100), 2)
            : 0;

        return [
            'total_interacoes' => $totalInteracoes,
            'interacoes_concluidas' => $interacoesConcluidas,
            'previstas_ate_hoje' => $previstasAteHoje,
            'percentual_total' => $percentualTotal,
            'percentual_previsto' => $percentualPrevisto,
        ];
    }
}
