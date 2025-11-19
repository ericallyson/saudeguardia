@php($tipo = $meta->tipo)
@php($valorPasAnterior = old('valor_pas', $valorPasAnterior ?? null))
@php($valorPadAnterior = old('valor_pad', $valorPadAnterior ?? null))

@if ($tipo === 'blood_pressure' && $valorPasAnterior === null && $valorPadAnterior === null && is_string($valorAnterior))
    @php($pressaoMatches = [])
    @if (preg_match('/^(\d{2,3})\s*[xX]\s*(\d{2,3})$/', trim($valorAnterior), $pressaoMatches))
        @php($valorPasAnterior = (int) ($pressaoMatches[1] ?? null))
        @php($valorPadAnterior = (int) ($pressaoMatches[2] ?? null))
    @endif
@endif

@if ($tipo === 'boolean')
    {{-- Sim / Não em estilo “pill” premium --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
        <div class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">
            Selecione uma opção
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3 w-full">
            <label class="group relative flex-1 cursor-pointer">
                <input
                    type="radio"
                    name="valor"
                    value="sim"
                    class="sr-only peer"
                    {{ $valorAnterior === 'sim' ? 'checked' : '' }}
                    required
                >
                <div
                    class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm
                           transition-all
                           peer-checked:border-indigo-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-400/30
                           group-hover:border-indigo-300"
                >
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 bg-white/90 text-[11px]
                                 peer-checked:border-indigo-300 peer-checked:bg-indigo-500 peer-checked:text-white">
                        ✓
                    </span>
                    <span>Sim</span>
                </div>
            </label>

            <label class="group relative flex-1 cursor-pointer">
                <input
                    type="radio"
                    name="valor"
                    value="nao"
                    class="sr-only peer"
                    {{ $valorAnterior === 'nao' ? 'checked' : '' }}
                    required
                >
                <div
                    class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm
                           transition-all
                           peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-slate-500/30
                           group-hover:border-slate-300"
                >
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 bg-white/90 text-[11px]
                                 peer-checked:border-slate-600 peer-checked:bg-slate-700 peer-checked:text-white">
                        ✕
                    </span>
                    <span>Não</span>
                </div>
            </label>
        </div>
    </div>

@elseif ($tipo === 'integer')
    {{-- Campo numérico --}}
    <input
        type="number"
        name="valor"
        id="valor"
        value="{{ $valorAnterior }}"
        step="0.01"
        lang="pt-BR"
        inputmode="decimal"
        class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-sm text-slate-900 shadow-sm
               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition"
        required
    >

@elseif ($tipo === 'blood_pressure')
    @php($valoresPressao = range(50, 220, 5))

    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-1.5">
            <label for="valor_pas" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                PAS — pressão sistólica
            </label>
            <div class="rounded-2xl border border-slate-200 bg-white/90 px-3 py-2.5 shadow-sm focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 transition">
                <select
                    name="valor_pas"
                    id="valor_pas"
                    class="mt-0 w-full border-none bg-transparent p-0 text-sm text-slate-900 focus:ring-0"
                    required
                >
                    <option value="">Selecione</option>
                    @foreach ($valoresPressao as $valor)
                        <option value="{{ $valor }}" @selected((string) $valorPasAnterior === (string) $valor)>{{ $valor }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-[11px] text-slate-500">Valor de cima (ex.: <span class="font-semibold">120</span>).</p>
        </div>

        <div class="space-y-1.5">
            <label for="valor_pad" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                PAD — pressão diastólica
            </label>
            <div class="rounded-2xl border border-slate-200 bg-white/90 px-3 py-2.5 shadow-sm focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 transition">
                <select
                    name="valor_pad"
                    id="valor_pad"
                    class="mt-0 w-full border-none bg-transparent p-0 text-sm text-slate-900 focus:ring-0"
                    required
                >
                    <option value="">Selecione</option>
                    @foreach ($valoresPressao as $valor)
                        <option value="{{ $valor }}" @selected((string) $valorPadAnterior === (string) $valor)>{{ $valor }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-[11px] text-slate-500">Valor de baixo (ex.: <span class="font-semibold">80</span>).</p>
        </div>
    </div>

    <p class="mt-3 text-xs text-slate-500">
        Escolha valores entre <span class="font-semibold">50</span> e <span class="font-semibold">220</span> mmHg (incrementos de 5).
        Exemplo de combinação: <span class="font-semibold">120 x 80</span>.
    </p>

@elseif ($tipo === 'scale')
    {{-- Escala 1–5 --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-3">
            <label for="valor" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                Escolha um número de 1 a 5
            </label>
            <p class="text-[11px] text-slate-500">
                1 = muito ruim &nbsp;&middot;&nbsp; 5 = muito bem
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white/90 px-3 py-2.5 shadow-sm focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 transition">
            <select
                name="valor"
                id="valor"
                class="mt-0 w-full border-none bg-transparent p-0 text-sm text-slate-900 focus:ring-0"
                required
            >
                <option value="">Selecione</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ (string) $valorAnterior === (string) $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

@else
    {{-- Texto livre --}}
    <div class="space-y-1.5">
        <label for="valor" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
            Compartilhe seus avanços
        </label>
        <textarea
            name="valor"
            id="valor"
            rows="4"
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-sm text-slate-900 shadow-sm
                   placeholder:text-slate-400
                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition"
            placeholder="Ex.: consegui caminhar 30 minutos hoje, me senti melhor após a atividade..."
            required
        >{{ $valorAnterior }}</textarea>
    </div>
@endif
