@extends('layouts.app')

@section('title', 'Pacientes — Saúde Guardiã')

@section('main')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pacientes</h1>
            <p class="text-gray-500">Gerencie as informações dos seus pacientes e acompanhe seus progressos.</p>
        </div>
        <a
            href="{{ route('pacientes.create') }}"
            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
        >
            ＋ Novo paciente
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="card p-6">
        <div class="overflow-x-auto rounded-lg border border-[#e3d7c3]">
            <table class="min-w-full table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">Paciente</th>
                        <th class="px-6 py-3 text-left">Plano</th>
                        <th class="px-6 py-3 text-left">Desde</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#f3ede1]">
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-semibold text-gray-800">{{ $paciente->nome }}</div>
                                <div class="text-gray-500">
                                    @if ($paciente->email)
                                        <span>{{ $paciente->email }}</span>
                                    @endif
                                    @if ($paciente->email && $paciente->telefone)
                                        <span class="mx-1">•</span>
                                    @endif
                                    @if ($paciente->telefone)
                                        <span>{{ $paciente->telefone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $paciente->plano ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $paciente->data_inicio?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $status = $paciente->status;
                                    $statusClasses = [
                                        'ativo' => 'bg-green-100 text-green-700',
                                        'inativo' => 'bg-red-100 text-red-700',
                                        'em_atendimento' => 'bg-yellow-100 text-yellow-700',
                                    ];
                                    $badgeClass = $statusClasses[$status] ?? 'bg-green-100 text-green-700';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $badgeClass }}">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('pacientes.edit', $paciente) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Tem certeza que deseja remover este paciente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum paciente cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $pacientes->links() }}
        </div>
    </div>
@endsection
