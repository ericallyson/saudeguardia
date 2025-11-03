@csrf

<div class="grid grid-cols-1 gap-6">
    <div>
        <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Meta</label>
        <input
            type="text"
            id="nome"
            name="nome"
            value="{{ old('nome', $meta->nome ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >
        @error('nome')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea
            id="descricao"
            name="descricao"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('descricao', $meta->descricao ?? '') }}</textarea>
        @error('descricao')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo da Meta</label>
        <select
            id="tipo"
            name="tipo"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Selecione um tipo</option>
            @foreach($tipos as $valor => $label)
                <option value="{{ $valor }}" @selected(old('tipo', $meta->tipo ?? '') === $valor)>{{ $label }}</option>
            @endforeach
        </select>
        @error('tipo')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 flex justify-end space-x-3">
    <a
        href="{{ route('metas.index') }}"
        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
    >
        Cancelar
    </a>
    <button
        type="submit"
        class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
    >
        Salvar
    </button>
</div>
