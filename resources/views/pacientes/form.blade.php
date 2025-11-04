@csrf

@php
    /** @var \App\Models\Paciente|null $paciente */
    $paciente = $paciente ?? new \App\Models\Paciente();
    $metas = collect($metas ?? []);
    $pacienteMetas = ($paciente->relationLoaded('metas') ? $paciente->metas : ($paciente->metas ?? collect()))->keyBy('id');
    $periodicidadesDisponiveis = \App\Models\Meta::PERIODICIDADES;
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
            <h3 class="text-md font-semibold text-gray-800 mb-4">Metas cadastradas</h3>

            @if ($metas->isEmpty())
                <p class="text-sm text-gray-500">Nenhuma meta cadastrada no sistema.</p>
            @else
                <div class="space-y-4">
                    @foreach ($metas as $meta)
                        @php
                            $metaOld = old('metas.' . $meta->id, []);
                            $metaPaciente = $pacienteMetas->get($meta->id);
                            $metaSelecionada = isset($metaOld['selected'])
                                ? (bool) $metaOld['selected']
                                : ($metaPaciente !== null);
                            $periodicidadeSelecionada = $metaOld['periodicidade']
                                ?? ($metaPaciente ? $metaPaciente->pivot->periodicidade : ($meta->periodicidade_padrao ?? ''));
                            $vencimentoSelecionado = $metaOld['vencimento']
                                ?? ($metaPaciente ? ($metaPaciente->pivot->vencimento ?? '') : '');
                        @endphp
                        <div class="border border-[#e3d7c3] rounded-lg p-4 bg-white/60">
                            <div class="flex items-start gap-3">
                                <div class="pt-1">
                                    <input
                                        type="checkbox"
                                        id="meta-{{ $meta->id }}"
                                        name="metas[{{ $meta->id }}][selected]"
                                        value="1"
                                        @checked($metaSelecionada)
                                    >
                                </div>
                                <div class="flex-1 space-y-4">
                                    <div class="flex flex-col">
                                        <label for="meta-{{ $meta->id }}" class="text-sm font-semibold text-gray-800">
                                            {{ $meta->nome }}
                                        </label>
                                        @if ($meta->descricao)
                                            <p class="text-sm text-gray-500">{{ $meta->descricao }}</p>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="meta-periodicidade-{{ $meta->id }}" class="block text-sm font-medium text-gray-700">
                                                Periodicidade
                                            </label>
                                            <select
                                                id="meta-periodicidade-{{ $meta->id }}"
                                                name="metas[{{ $meta->id }}][periodicidade]"
                                                class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Selecione</option>
                                                @foreach ($periodicidadesDisponiveis as $valor => $label)
                                                    <option value="{{ $valor }}" @selected($periodicidadeSelecionada === $valor)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('metas.' . $meta->id . '.periodicidade')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="meta-vencimento-{{ $meta->id }}" class="block text-sm font-medium text-gray-700">
                                                Vencimento
                                            </label>
                                            <input
                                                type="date"
                                                id="meta-vencimento-{{ $meta->id }}"
                                                name="metas[{{ $meta->id }}][vencimento]"
                                                value="{{ $vencimentoSelecionado }}"
                                                class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                                            >
                                            @error('metas.' . $meta->id . '.vencimento')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
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

<div class="mt-6 flex justify-end space-x-3">
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
