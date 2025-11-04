@extends('layouts.app')

@section('title', 'Metas — Saúde Guardiã')

@section('main')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Metas</h1>
            <p class="text-gray-500">Gerencie as metas que serão monitoradas pelos pacientes.</p>
        </div>
        <a
            href="{{ route('metas.create') }}"
            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
        >
            Nova meta
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="card p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Periodicidade padrão</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Última atualização</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($metas as $meta)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $meta->nome }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ \App\Models\Meta::TIPOS[$meta->tipo] ?? $meta->tipo }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $meta->periodicidade_padrao ? (\App\Models\Meta::PERIODICIDADES[$meta->periodicidade_padrao] ?? $meta->periodicidade_padrao) : '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $meta->updated_at?->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('metas.edit', $meta) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                <form action="{{ route('metas.destroy', $meta) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Tem certeza que deseja remover esta meta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma meta cadastrada ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $metas->links() }}
        </div>
    </div>
@endsection
