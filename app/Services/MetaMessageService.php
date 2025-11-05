<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\MetaMessage;
use App\Models\Paciente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MetaMessageService
{
    private const PERIOD_INTERVALS = [
        'diario' => 1,
        'semanal' => 7,
        'quinzenal' => 15,
        'dia_sim_dia_nao' => 2,
    ];

    public function rebuildForAllPacientes(): void
    {
        Paciente::query()
            ->with('metas')
            ->whereHas('metas')
            ->chunkById(50, function ($pacientes): void {
                foreach ($pacientes as $paciente) {
                    $this->rebuildForPaciente($paciente);
                }
            });
    }

    public function rebuildForPaciente(Paciente $paciente): void
    {
        $paciente->load('metas');

        DB::transaction(function () use ($paciente): void {
            $paciente->metaMessages()->delete();

            if ($paciente->metas->isEmpty()) {
                return;
            }

            $telefone = $this->formatPhoneNumber($paciente);

            if (! $telefone) {
                return;
            }

            foreach ($paciente->metas as $meta) {
                $datasEnvio = $this->buildSchedule($paciente, $meta);

                foreach ($datasEnvio as $dataEnvio) {
                    MetaMessage::create([
                        'paciente_id' => $paciente->id,
                        'meta_id' => $meta->id,
                        'paciente_nome' => $paciente->nome,
                        'telefone' => $telefone,
                        'token' => Str::uuid()->toString(),
                        'data_envio' => $dataEnvio,
                        'status' => 'pendente',
                    ]);
                }
            }
        });
    }

    private function buildSchedule(Paciente $paciente, Meta $meta): Collection
    {
        $inicio = $paciente->data_inicio instanceof Carbon
            ? $paciente->data_inicio->copy()
            : Carbon::now();

        $inicio = $inicio->isPast() ? Carbon::now() : $inicio;
        $inicio = $inicio->startOfDay();

        $vencimento = $meta->pivot?->vencimento
            ? Carbon::parse($meta->pivot->vencimento)->endOfDay()
            : $inicio->copy()->addMonth();

        if ($inicio->gt($vencimento)) {
            return collect();
        }

        $intervalo = self::PERIOD_INTERVALS[$meta->pivot->periodicidade ?? '']
            ?? ($meta->periodicidade_padrao
                ? (self::PERIOD_INTERVALS[$meta->periodicidade_padrao] ?? 7)
                : 7);

        $datas = collect();
        $dataAtual = $inicio->copy();
        $limiteIteracoes = 365;

        while ($dataAtual->lte($vencimento) && $limiteIteracoes > 0) {
            $datas->push($dataAtual->copy());
            $dataAtual->addDays($intervalo);
            $limiteIteracoes--;
        }

        return $datas;
    }

    private function formatPhoneNumber(Paciente $paciente): ?string
    {
        $numero = $paciente->whatsapp_numero ?: $paciente->telefone;

        if (! $numero) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $numero);

        if (! $digits) {
            return null;
        }

        if (! str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }

        return '+' . $digits;
    }
}
