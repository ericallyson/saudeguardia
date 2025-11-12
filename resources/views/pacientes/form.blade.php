@csrf

@php
    /** @var \App\Models\Paciente|null $paciente */
    $paciente = $paciente ?? new \App\Models\Paciente();
    $metas = collect($metas ?? []);
    $diasSemanaOptions = collect($diasSemanaOptions ?? [
        'monday' => 'Segunda-feira',
        'tuesday' => 'Terça-feira',
        'wednesday' => 'Quarta-feira',
        'thursday' => 'Quinta-feira',
        'friday' => 'Sexta-feira',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
    ]);

    $pacienteMetas = collect();

    if ($paciente->exists && $paciente->relationLoaded('metas')) {
        $pacienteMetas = collect($paciente->metas ?? []);
    }

    $metasSelecionadas = collect(old('metas', []));

    if ($metasSelecionadas->isEmpty() && $pacienteMetas->isNotEmpty()) {
        $metasSelecionadas = $pacienteMetas->map(function ($meta) use ($diasSemanaOptions) {
            $dias = $meta->pivot->dias_semana ?? [];

            if (is_string($dias)) {
                $decoded = json_decode($dias, true);
                $dias = is_array($decoded) ? $decoded : [];
            }

            $diasNormalizados = collect(is_array($dias) ? $dias : [])
                ->filter(fn ($dia) => is_string($dia))
                ->map(fn ($dia) => strtolower($dia))
                ->filter(fn ($dia) => $diasSemanaOptions->has($dia))
                ->values()
                ->all();

            $vencimento = $meta->pivot->vencimento ?? null;
            $vencimento = $vencimento
                ? \Illuminate\Support\Carbon::parse($vencimento)->format('Y-m-d')
                : '';

            $horarios = $meta->pivot->horarios ?? [];

            if (is_string($horarios)) {
                $decodedHorarios = json_decode($horarios, true);
                $horarios = is_array($decodedHorarios) ? $decodedHorarios : [];
            }

            if (! is_array($horarios) || empty($horarios)) {
                $horarioUnico = $meta->pivot->horario ?? null;
                $horarios = $horarioUnico ? [$horarioUnico] : [];
            }

            $horariosNormalizados = collect($horarios)
                ->filter(fn ($horario) => is_string($horario) && $horario !== '')
                ->map(fn ($horario) => substr($horario, 0, 5))
                ->filter(fn ($horario) => preg_match('/^\d{2}:\d{2}$/', $horario) === 1)
                ->unique()
                ->sort()
                ->take(3)
                ->values();

            if ($horariosNormalizados->isEmpty()) {
                $horariosNormalizados = collect(['09:00']);
            }

            return [
                'meta_id' => $meta->id,
                'vencimento' => $vencimento,
                'horarios' => $horariosNormalizados->all(),
                'dias_semana' => $diasNormalizados,
            ];
        })->values();
    }

    $metasSelecionadas = $metasSelecionadas->map(function ($meta) use ($diasSemanaOptions) {
        $dias = $meta['dias_semana'] ?? [];

        if (! is_array($dias)) {
            $dias = [];
        }

        $diasNormalizados = collect($dias)
            ->filter(fn ($dia) => is_string($dia))
            ->map(fn ($dia) => strtolower($dia))
            ->filter(fn ($dia) => $diasSemanaOptions->has($dia))
            ->values()
            ->all();

        $horarios = $meta['horarios'] ?? [];

        if (! is_array($horarios) || empty($horarios)) {
            $horarioUnico = $meta['horario'] ?? null;
            $horarios = $horarioUnico ? [$horarioUnico] : [];
        }

        $horariosNormalizados = collect($horarios)
            ->filter(fn ($horario) => is_string($horario) && $horario !== '')
            ->map(fn ($horario) => substr($horario, 0, 5))
            ->filter(fn ($horario) => preg_match('/^\d{2}:\d{2}$/', $horario) === 1)
            ->unique()
            ->sort()
            ->take(3)
            ->values();

        if ($horariosNormalizados->isEmpty()) {
            $horariosNormalizados = collect(['09:00']);
        }

        return [
            'meta_id' => $meta['meta_id'] ?? '',
            'vencimento' => $meta['vencimento'] ?? '',
            'horarios' => $horariosNormalizados->all(),
            'dias_semana' => $diasNormalizados,
        ];
    });

    $metaIndexCounter = $metasSelecionadas->keys()
        ->filter(fn ($key) => is_numeric($key))
        ->map(fn ($key) => (int) $key)
        ->max();

    $metaIndexCounter = is_numeric($metaIndexCounter)
        ? $metaIndexCounter + 1
        : $metasSelecionadas->count();
@endphp

<div class="space-y-8">
    <div class="card p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Informações pessoais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome completo</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="{{ old('nome', $paciente->nome ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                    required
                >
                @error('nome')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $paciente->email ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input
                    type="text"
                    id="telefone"
                    name="telefone"
                    value="{{ old('telefone', $paciente->telefone ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('telefone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de nascimento</label>
                <input
                    type="date"
                    id="data_nascimento"
                    name="data_nascimento"
                    value="{{ old('data_nascimento', isset($paciente->data_nascimento) ? $paciente->data_nascimento->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('data_nascimento')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="plano" class="block text-sm font-medium text-gray-700">Plano</label>
                <input
                    type="text"
                    id="plano"
                    name="plano"
                    value="{{ old('plano', $paciente->plano ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('plano')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="data_inicio" class="block text-sm font-medium text-gray-700">Data de início</label>
                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="{{ old('data_inicio', isset($paciente->data_inicio) ? $paciente->data_inicio->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('data_inicio')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select
                    id="status"
                    name="status"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                    required
                >
                    @php
                        $statusOptions = [
                            'ativo' => 'Ativo',
                            'em_atendimento' => 'Em atendimento',
                            'inativo' => 'Inativo',
                        ];
                    @endphp
                    <option value="">Selecione um status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $paciente->status ?? 'ativo') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Informações clínicas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="peso_inicial" class="block text-sm font-medium text-gray-700">Peso inicial (kg)</label>
                <input
                    type="number"
                    step="0.1"
                    id="peso_inicial"
                    name="peso_inicial"
                    value="{{ old('peso_inicial', $paciente->peso_inicial ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('peso_inicial')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="altura_cm" class="block text-sm font-medium text-gray-700">Altura (cm)</label>
                <input
                    type="number"
                    id="altura_cm"
                    name="altura_cm"
                    value="{{ old('altura_cm', $paciente->altura_cm ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('altura_cm')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="circunferencia_abdominal" class="block text-sm font-medium text-gray-700">Circunferência abdominal (cm)</label>
                <input
                    type="number"
                    step="0.1"
                    id="circunferencia_abdominal"
                    name="circunferencia_abdominal"
                    value="{{ old('circunferencia_abdominal', $paciente->circunferencia_abdominal ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('circunferencia_abdominal')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6">
            <label for="condicoes_medicas" class="block text-sm font-medium text-gray-700">Condições médicas</label>
            <textarea
                id="condicoes_medicas"
                name="condicoes_medicas"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
            >{{ old('condicoes_medicas', $paciente->condicoes_medicas ?? '') }}</textarea>
            @error('condicoes_medicas')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Metas do tratamento</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="peso_meta" class="block text-sm font-medium text-gray-700">Peso meta (kg)</label>
                <input
                    type="number"
                    step="0.1"
                    id="peso_meta"
                    name="peso_meta"
                    value="{{ old('peso_meta', $paciente->peso_meta ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('peso_meta')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="prazo_meses" class="block text-sm font-medium text-gray-700">Prazo (meses)</label>
                <input
                    type="number"
                    id="prazo_meses"
                    name="prazo_meses"
                    value="{{ old('prazo_meses', $paciente->prazo_meses ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('prazo_meses')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6">
            <label for="atividade_fisica" class="block text-sm font-medium text-gray-700">Atividade física recomendada</label>
            <input
                type="text"
                id="atividade_fisica"
                name="atividade_fisica"
                value="{{ old('atividade_fisica', $paciente->atividade_fisica ?? '') }}"
                placeholder="Ex.: Academia 3x/semana, 1h por sessão"
                class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
            >
            @error('atividade_fisica')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">Metas vinculadas ao paciente</h3>
                    <p class="text-sm text-gray-500">Escolha quantas metas forem necessárias e personalize dias, horários e vencimento.</p>
                </div>
                @if ($metas->isNotEmpty())
                    <button
                        type="button"
                        id="adicionar-meta"
                        class="inline-flex items-center rounded-md border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 shadow-sm hover:bg-indigo-100"
                    >
                        Adicionar meta
                    </button>
                @endif
            </div>

            <p
                class="mt-4 text-sm text-gray-500"
                data-meta-empty
                @if ($metasSelecionadas->isNotEmpty())
                    hidden
                @endif
            >
                Nenhuma meta adicionada. Clique em &ldquo;Adicionar meta&rdquo; para começar.
            </p>

            @if ($metas->isEmpty())
                <p class="mt-4 text-sm text-red-500">
                    Cadastre metas no sistema para poder vinculá-las aos pacientes.
                </p>
            @endif

            <div id="meta-entries" class="mt-4 space-y-4">
                @foreach ($metasSelecionadas as $index => $metaSelecionada)
                    @include('pacientes.partials.meta-entry', [
                        'index' => $index,
                        'metaSelecionada' => $metaSelecionada,
                        'metas' => $metas,
                        'diasSemanaOptions' => $diasSemanaOptions,
                    ])
                @endforeach
            </div>
        </div>

        <template id="meta-entry-template">
            @include('pacientes.partials.meta-entry', [
                'index' => '__INDEX__',
                'metaSelecionada' => [
                    'meta_id' => '',
                    'vencimento' => '',
                    'horarios' => ['09:00'],
                    'dias_semana' => [],
                ],
                'metas' => $metas,
                'diasSemanaOptions' => $diasSemanaOptions,
            ])
        </template>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Lembretes via WhatsApp</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="whatsapp_numero" class="block text-sm font-medium text-gray-700">Número do WhatsApp</label>
                <input
                    type="text"
                    id="whatsapp_numero"
                    name="whatsapp_numero"
                    value="{{ old('whatsapp_numero', $paciente->whatsapp_numero ?? '') }}"
                    placeholder="(11) 99999-9999"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @error('whatsapp_numero')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="whatsapp_frequencia" class="block text-sm font-medium text-gray-700">Frequência dos lembretes</label>
                <select
                    id="whatsapp_frequencia"
                    name="whatsapp_frequencia"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">Selecione</option>
                    @php
                        $frequencias = [
                            'diario' => 'Diário',
                            '2x_dia' => '2x por dia',
                            '3x_dia' => '3x por dia',
                            'semanal' => 'Semanal',
                        ];
                    @endphp
                    @foreach ($frequencias as $value => $label)
                        <option value="{{ $value }}" @selected(old('whatsapp_frequencia', $paciente->whatsapp_frequencia ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('whatsapp_frequencia')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    @if ($paciente->exists)
        <form action="{{ route('pacientes.cancelar-metas', $paciente) }}" method="POST" class="inline-flex">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-100"
            >
                Cancelar metas futuras
            </button>
        </form>
    @endif

    <div class="flex justify-end gap-3">
        <a
            href="{{ route('pacientes.index') }}"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
            Cancelar
        </a>
        <button
            type="submit"
            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
        >
            Salvar paciente
        </button>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('meta-entries');
            const templateContainer = document.getElementById('meta-entry-template');
            const addButton = document.getElementById('adicionar-meta');
            const emptyMessage = document.querySelector('[data-meta-empty]');
            let metaIndex = Number({{ $metaIndexCounter }}) || 0;
            const MAX_HORARIOS = 3;

            if (!container) {
                return;
            }

            const updateEmptyState = () => {
                if (!emptyMessage) {
                    return;
                }

                if (container.querySelector('[data-meta-entry]')) {
                    emptyMessage.setAttribute('hidden', 'hidden');
                } else {
                    emptyMessage.removeAttribute('hidden');
                }
            };

            const attachEntryListeners = (entry) => {
                if (!entry) {
                    return;
                }

                const removeButton = entry.querySelector('[data-remove-meta]');
                if (removeButton) {
                    removeButton.addEventListener('click', () => {
                        entry.remove();
                        updateEmptyState();
                    });
                }

                const horariosContainer = entry.querySelector('[data-horarios-container]');
                const addHorarioButton = entry.querySelector('[data-add-horario]');
                const horarioTemplate = entry.querySelector('[data-horario-template]');

                if (!horariosContainer) {
                    return;
                }

                const updateHorarioState = () => {
                    const entries = horariosContainer.querySelectorAll('[data-horario-entry]');

                    if (addHorarioButton) {
                        if (entries.length >= MAX_HORARIOS) {
                            addHorarioButton.setAttribute('disabled', 'disabled');
                            addHorarioButton.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            addHorarioButton.removeAttribute('disabled');
                            addHorarioButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }

                    entries.forEach((horarioEntry) => {
                        const removeHorarioButton = horarioEntry.querySelector('[data-remove-horario]');

                        if (!removeHorarioButton) {
                            return;
                        }

                        if (entries.length <= 1) {
                            removeHorarioButton.setAttribute('disabled', 'disabled');
                            removeHorarioButton.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            removeHorarioButton.removeAttribute('disabled');
                            removeHorarioButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    });
                };

                const attachHorarioEntryListeners = (horarioEntry) => {
                    if (!horarioEntry) {
                        return;
                    }

                    const removeHorarioButton = horarioEntry.querySelector('[data-remove-horario]');

                    if (removeHorarioButton) {
                        removeHorarioButton.addEventListener('click', () => {
                            const entries = horariosContainer.querySelectorAll('[data-horario-entry]');

                            if (entries.length <= 1) {
                                return;
                            }

                            horarioEntry.remove();
                            updateHorarioState();
                        });
                    }
                };

                horariosContainer.querySelectorAll('[data-horario-entry]').forEach(attachHorarioEntryListeners);

                if (addHorarioButton && horarioTemplate) {
                    addHorarioButton.addEventListener('click', () => {
                        const total = horariosContainer.querySelectorAll('[data-horario-entry]').length;

                        if (total >= MAX_HORARIOS) {
                            return;
                        }

                        let newEntry = null;

                        if (horarioTemplate instanceof HTMLTemplateElement) {
                            const templateContent = horarioTemplate.content.firstElementChild;
                            newEntry = templateContent ? templateContent.cloneNode(true) : null;
                        } else {
                            newEntry = horarioTemplate.cloneNode(true);
                        }

                        if (!newEntry || typeof newEntry.querySelector !== 'function') {
                            return;
                        }

                        if (newEntry instanceof HTMLElement) {
                            newEntry.removeAttribute('data-horario-template');
                        }

                        if (!newEntry.hasAttribute('data-horario-entry')) {
                            newEntry.setAttribute('data-horario-entry', '');
                        }

                        const input = newEntry.querySelector('input[type="time"]');
                        if (input) {
                            input.value = '';
                        }

                        horariosContainer.appendChild(newEntry);
                        attachHorarioEntryListeners(newEntry);
                        updateHorarioState();
                    });
                }

                updateHorarioState();
            };

            container.querySelectorAll('[data-meta-entry]').forEach(attachEntryListeners);
            updateEmptyState();

            if (addButton && templateContainer) {
                addButton.addEventListener('click', () => {
                    const template = templateContainer.innerHTML;

                    if (!template) {
                        return;
                    }

                    const html = template.replace(/__INDEX__/g, metaIndex);
                    metaIndex += 1;

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    const entry = wrapper.firstElementChild;

                    if (!entry) {
                        return;
                    }

                    attachEntryListeners(entry);
                    container.appendChild(entry);
                    updateEmptyState();
                });
            }
        });
    </script>
@endpush
