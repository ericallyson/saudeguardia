@php
    $metas = collect($metas ?? []);
    $diasSemanaOptions = collect($diasSemanaOptions ?? []);

    $metaId = $metaSelecionada['meta_id'] ?? '';
    $vencimento = $metaSelecionada['vencimento'] ?? '';
    $horario = $metaSelecionada['horario'] ?? '09:00';
    $horario = is_string($horario) && $horario !== '' ? substr($horario, 0, 5) : '09:00';

    $diasSelecionados = collect($metaSelecionada['dias_semana'] ?? [])
        ->filter(fn ($dia) => is_string($dia))
        ->map(fn ($dia) => strtolower($dia))
        ->filter(fn ($dia) => $diasSemanaOptions->has($dia))
        ->values()
        ->all();

@endphp

<div class="border border-[#e3d7c3] rounded-lg bg-white/70 p-4" data-meta-entry>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Meta</label>
                <select
                    name="metas[{{ $index }}][meta_id]"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">Selecione uma meta</option>
                    @foreach ($metas as $metaOption)
                        <option value="{{ $metaOption->id }}" @selected((string) $metaId === (string) $metaOption->id)>
                            {{ $metaOption->nome }}
                        </option>
                    @endforeach
                </select>
                @if (is_numeric($index))
                    @error('metas.' . $index . '.meta_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
            <div class="flex items-center justify-end">
                <button type="button" class="text-sm font-medium text-red-600 hover:text-red-700" data-remove-meta>
                    Remover
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Vencimento</label>
                <input
                    type="date"
                    name="metas[{{ $index }}][vencimento]"
                    value="{{ $vencimento }}"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @if (is_numeric($index))
                    @error('metas.' . $index . '.vencimento')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Horário do envio</label>
                <input
                    type="time"
                    name="metas[{{ $index }}][horario]"
                    value="{{ $horario }}"
                    step="60"
                    class="mt-1 block w-full rounded-lg border border-[#e3d7c3] bg-white/90 p-2.5 focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                >
                @if (is_numeric($index))
                    @error('metas.' . $index . '.horario')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
            <div class="md:col-span-3">
                <span class="block text-sm font-medium text-gray-700">Dias da semana</span>
                <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                    @foreach ($diasSemanaOptions as $valor => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="metas[{{ $index }}][dias_semana][]"
                                value="{{ $valor }}"
                                @checked(in_array($valor, $diasSelecionados, true))
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @if (is_numeric($index))
                    @error('metas.' . $index . '.dias_semana')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('metas.' . $index . '.dias_semana.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </div>
    </div>
</div>
