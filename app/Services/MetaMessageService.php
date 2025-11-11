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
    private const WEEKDAY_MAP = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
        'sunday' => 7,
    ];

    private const DEFAULT_TIME = '09:00';

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

    public function rebuildForMeta(Meta $meta): void
    {
        $meta->load('pacientes');

        if ($meta->pacientes->isEmpty()) {
            return;
        }

        foreach ($meta->pacientes as $paciente) {
            $this->rebuildForPaciente($paciente);
        }
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
                        'enviado_em' => null,
                    ]);
                }
            }
        });
    }

    private function buildSchedule(Paciente $paciente, Meta $meta): Collection
    {
        $horarios = $this->resolveHorarios($meta);

        if (empty($horarios)) {
            return collect();
        }

        $inicio = $paciente->data_inicio instanceof Carbon
            ? $paciente->data_inicio->copy()
            : Carbon::now();

        $inicio = $inicio->isPast() ? Carbon::now() : $inicio;
        $inicio = $inicio->startOfDay()->setTimeFromTimeString($horarios[0]);

        $diasSelecionados = $this->resolveDiasSemana($meta);

        if (empty($diasSelecionados)) {
            return collect();
        }

        $diasPermitidos = array_unique(array_map(fn (string $dia) => self::WEEKDAY_MAP[$dia], $diasSelecionados));

        $vencimento = $meta->pivot?->vencimento
            ? Carbon::parse($meta->pivot->vencimento)->endOfDay()
            : $inicio->copy()->addMonth();

        if ($inicio->gt($vencimento)) {
            return collect();
        }

        $datas = collect();

        for ($dataAtual = $inicio->copy(); $dataAtual->lte($vencimento); $dataAtual->addDay()) {
            if (in_array($dataAtual->dayOfWeekIso, $diasPermitidos, true)) {
                foreach ($horarios as $horario) {
                    $datas->push($dataAtual->copy()->setTimeFromTimeString($horario));
                }
            }
        }

        return $datas
            ->sortBy(fn (Carbon $data) => $data->timestamp)
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function resolveHorarios(Meta $meta): array
    {
        $horarios = $meta->pivot?->horarios ?? [];

        if (is_string($horarios)) {
            $decoded = json_decode($horarios, true);
            $horarios = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($horarios) || empty($horarios)) {
            $horarioUnico = $meta->pivot?->horario ?? null;
            $horarios = $horarioUnico ? [$horarioUnico] : [];
        }

        $normalizados = collect($horarios)
            ->map(fn ($horario) => $this->normalizeHorario($horario))
            ->filter()
            ->unique()
            ->sort()
            ->take(3)
            ->values()
            ->all();

        if (empty($normalizados)) {
            return [self::DEFAULT_TIME];
        }

        return $normalizados;
    }

    private function normalizeHorario(mixed $horario): ?string
    {
        if (! is_string($horario) || $horario === '') {
            return null;
        }

        $normalized = substr($horario, 0, 5);

        if (preg_match('/^\d{2}:\d{2}$/', $normalized) === 1) {
            return $normalized;
        }

        if (preg_match('/^\d{2}:\d{2}$/', $horario) === 1) {
            return $horario;
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function resolveDiasSemana(Meta $meta): array
    {
        $dias = $meta->pivot?->dias_semana ?? [];

        if (is_string($dias)) {
            $decoded = json_decode($dias, true);
            $dias = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($dias)) {
            return [];
        }

        return collect($dias)
            ->filter(fn ($dia) => is_string($dia))
            ->map(fn ($dia) => strtolower($dia))
            ->filter(fn ($dia) => array_key_exists($dia, self::WEEKDAY_MAP))
            ->unique()
            ->values()
            ->all();
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
