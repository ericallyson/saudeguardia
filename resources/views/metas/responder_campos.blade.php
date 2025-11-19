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
    <div class="flex items-center gap-4">
        <label class="inline-flex items-center gap-2">
            <input type="radio" name="valor" value="sim" class="text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ $valorAnterior === 'sim' ? 'checked' : '' }} required>
            <span>Sim</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="radio" name="valor" value="nao" class="text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ $valorAnterior === 'nao' ? 'checked' : '' }} required>
            <span>Não</span>
        </label>
    </div>
@elseif ($tipo === 'integer')
    <input type="number" name="valor" id="valor" value="{{ $valorAnterior }}" step="0.01" lang="pt-BR" inputmode="decimal" class="w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" required>
@elseif ($tipo === 'blood_pressure')
    @php($valoresPressao = range(50, 220, 5))
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="valor_pas" class="block text-sm font-medium text-slate-700">PAS (pressão sistólica)</label>
            <select name="valor_pas" id="valor_pas" class="mt-1 w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" required>
                <option value="">Selecione</option>
                @foreach ($valoresPressao as $valor)
                    <option value="{{ $valor }}" @selected((string) $valorPasAnterior === (string) $valor)>{{ $valor }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="valor_pad" class="block text-sm font-medium text-slate-700">PAD (pressão diastólica)</label>
            <select name="valor_pad" id="valor_pad" class="mt-1 w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" required>
                <option value="">Selecione</option>
                @foreach ($valoresPressao as $valor)
                    <option value="{{ $valor }}" @selected((string) $valorPadAnterior === (string) $valor)>{{ $valor }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <p class="mt-2 text-sm text-slate-500">Escolha valores entre 50 e 220 mmHg (incrementos de 5). Exemplo: 120 x 80.</p>
@elseif ($tipo === 'scale')
    <select name="valor" id="valor" class="w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" required>
        <option value="">Selecione</option>
        @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}" {{ (string) $valorAnterior === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>
@else
    <textarea name="valor" id="valor" rows="4" class="w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" placeholder="Compartilhe seus avanços" required>{{ $valorAnterior }}</textarea>
@endif
