@php($tipo = $meta->tipo)

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
    <input type="number" name="valor" id="valor" value="{{ $valorAnterior }}" class="w-full rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3" required>
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
