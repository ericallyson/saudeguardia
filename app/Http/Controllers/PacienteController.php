<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\Paciente;
use App\Services\MetaMessageService;
use App\Services\PacienteDashboardService;
use App\Services\PatientReportPdfBuilder;
use App\Services\WhatsappService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class PacienteController extends Controller
{
    private const DIAS_SEMANA = [
        'monday' => 'Segunda-feira',
        'tuesday' => 'Terça-feira',
        'wednesday' => 'Quarta-feira',
        'thursday' => 'Quinta-feira',
        'friday' => 'Sexta-feira',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
    ];

    public function __construct(
        private readonly MetaMessageService $metaMessageService,
        private readonly PacienteDashboardService $dashboardService,
        private readonly WhatsappService $whatsappService,
        private readonly PatientReportPdfBuilder $reportPdfBuilder,
    )
    {
    }

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request): View
    {
        $pacientes = $request->user()
            ->pacientes()
            ->latest()
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    public function dashboard(Request $request, Paciente $paciente): View
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $paciente->load('metas');

        $engajamento = $this->dashboardService->calcularEngajamento($paciente);
        $andamento = $this->dashboardService->calcularAndamentoTratamento($paciente);
        $metasFuturas = $this->dashboardService->listarMetasFuturas($paciente);
        $metaCharts = $this->dashboardService->construirGraficosMetas($paciente);

        return view('pacientes.dashboard', [
            'paciente' => $paciente,
            'engajamento' => $engajamento,
            'andamento' => $andamento,
            'metasFuturas' => $metasFuturas,
            'metaCharts' => $metaCharts,
        ]);
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(): View
    {
        $metas = Meta::query()
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $diasSemanaOptions = self::DIAS_SEMANA;

        return view('pacientes.create', compact('metas', 'diasSemanaOptions'));
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePaciente($request);
        $metas = $this->validatePacienteMetas($request);

        $paciente = $request->user()->pacientes()->create($data);

        $this->syncPacienteMetas($paciente, $metas);
        $this->metaMessageService->rebuildForPaciente($paciente);

        return redirect()->route('pacientes.index')->with('success', 'Paciente cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Request $request, Paciente $paciente): View
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $paciente->load('metas');
        $metas = Meta::query()
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $diasSemanaOptions = self::DIAS_SEMANA;

        return view('pacientes.edit', compact('paciente', 'metas', 'diasSemanaOptions'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $data = $this->validatePaciente($request);
        $metas = $this->validatePacienteMetas($request);

        $paciente->update($data);
        $this->syncPacienteMetas($paciente, $metas);
        $this->metaMessageService->rebuildForPaciente($paciente);

        return redirect()->route('pacientes.index')->with('success', 'Paciente atualizado com sucesso.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Request $request, Paciente $paciente): RedirectResponse
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente removido com sucesso.');
    }

    public function enviarAcompanhamento(Request $request, Paciente $paciente): RedirectResponse
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $numero = $paciente->whatsapp_numero ?: $paciente->telefone;

        if (! $numero) {
            return redirect()
                ->back()
                ->with('error', 'Cadastre um número de WhatsApp para o paciente antes de enviar o acompanhamento.');
        }

        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return redirect()
                ->back()
                ->with('error', 'Conecte sua instância de WhatsApp nas configurações para enviar acompanhamentos.');
        }

        $paciente->load('metas');

        $engajamento = $this->dashboardService->calcularEngajamento($paciente);
        $andamento = $this->dashboardService->calcularAndamentoTratamento($paciente);
        $metasFuturas = $this->dashboardService->listarMetasFuturas($paciente)->take(10);

        $reportUrl = URL::temporarySignedRoute(
            'pacientes.relatorios.publico',
            Carbon::now()->addDays(7),
            ['paciente' => $paciente],
        );

        $fileName = sprintf(
            'relatorio-%s-%s.pdf',
            Str::slug($paciente->nome ?: 'paciente'),
            Carbon::now()->format('YmdHis'),
        );

        $caption = sprintf(
            'Relatório de acompanhamento de %s. Veja mais em: %s',
            $paciente->nome,
            $reportUrl,
        );

        try {
            $pdfContents = $this->reportPdfBuilder->build(
                $paciente,
                $engajamento,
                $andamento,
                $metasFuturas,
                $reportUrl,
            );

            $this->whatsappService->sendDocument(
                $user,
                $numero,
                $fileName,
                $caption,
                $pdfContents,
            );
        } catch (Throwable $exception) {
            Log::error('Erro ao enviar relatório de acompanhamento via WhatsApp.', [
                'paciente_id' => $paciente->id,
                'user_id' => $user->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Não foi possível enviar o relatório de acompanhamento. Tente novamente mais tarde.');
        }

        return redirect()
            ->back()
            ->with('success', 'Relatório de acompanhamento enviado com sucesso pelo WhatsApp.');
    }

    /**
     * Validate patient data.
     */
    protected function validatePaciente(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'data_nascimento' => ['nullable', 'date'],
            'plano' => ['nullable', 'string', 'max:255'],
            'data_inicio' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'peso_inicial' => ['nullable', 'numeric', 'min:0'],
            'altura_cm' => ['nullable', 'integer', 'min:0'],
            'circunferencia_abdominal' => ['nullable', 'numeric', 'min:0'],
            'condicoes_medicas' => ['nullable', 'string'],
            'peso_meta' => ['nullable', 'numeric', 'min:0'],
            'prazo_meses' => ['nullable', 'integer', 'min:0'],
            'atividade_fisica' => ['nullable', 'string', 'max:255'],
            'whatsapp_numero' => ['nullable', 'string', 'max:30'],
            'whatsapp_frequencia' => ['nullable', 'string', 'max:50'],
        ]);
    }

    protected function validatePacienteMetas(Request $request): array
    {
        $metasRequest = $request->input('metas', []);

        if (! is_array($metasRequest)) {
            return [];
        }

        $metasDisponiveis = Meta::pluck('id')->map(fn ($id) => (int) $id)->all();
        $diasValidos = array_keys(self::DIAS_SEMANA);

        $metasValidadas = [];

        foreach (array_values($metasRequest) as $metaDados) {
            if (! is_array($metaDados)) {
                continue;
            }

            $validator = Validator::make($metaDados, [
                'meta_id' => ['required', Rule::in($metasDisponiveis)],
                'vencimento' => ['nullable', 'date'],
                'horarios' => ['required', 'array', 'min:1', 'max:3'],
                'horarios.*' => ['required', 'date_format:H:i'],
                'dias_semana' => ['required', 'array', 'min:1'],
                'dias_semana.*' => ['required', Rule::in($diasValidos)],
            ], [], [
                'meta_id' => 'meta',
                'vencimento' => 'vencimento',
                'horarios' => 'horários',
                'dias_semana' => 'dias da semana',
            ]);

            $dadosValidados = $validator->validate();

            $horarios = collect($dadosValidados['horarios'])
                ->filter(fn ($horario) => is_string($horario) && $horario !== '')
                ->map(fn ($horario) => substr($horario, 0, 5))
                ->filter(fn ($horario) => preg_match('/^\d{2}:\d{2}$/', $horario) === 1)
                ->unique()
                ->values()
                ->all();

            if (empty($horarios)) {
                continue;
            }

            $metasValidadas[] = [
                'meta_id' => (int) $dadosValidados['meta_id'],
                'vencimento' => $dadosValidados['vencimento'] ?? null,
                'horarios' => $horarios,
                'dias_semana' => array_values(array_unique($dadosValidados['dias_semana'])),
            ];
        }

        return $metasValidadas;
    }

    protected function syncPacienteMetas(Paciente $paciente, array $metas): void
    {
        $paciente->metas()->detach();

        foreach ($metas as $metaDados) {
            $horarios = $metaDados['horarios'];
            $paciente->metas()->attach($metaDados['meta_id'], [
                'vencimento' => $metaDados['vencimento'] ?: null,
                'horario' => $horarios[0] ?? null,
                'horarios' => json_encode($horarios),
                'dias_semana' => json_encode($metaDados['dias_semana']),
            ]);
        }
    }

    public function cancelarMetas(Request $request, Paciente $paciente): RedirectResponse
    {
        $this->ensurePacienteBelongsToUser($request, $paciente);

        $paciente->metaMessages()
            ->where('data_envio', '>=', Carbon::now())
            ->delete();

        return redirect()
            ->route('pacientes.edit', $paciente)
            ->with('success', 'Metas futuras canceladas com sucesso.');
    }

    private function ensurePacienteBelongsToUser(Request $request, Paciente $paciente): void
    {
        if ((int) $paciente->user_id !== (int) $request->user()->getKey()) {
            abort(404);
        }
    }
}
