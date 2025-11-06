@extends('layouts.app')

@section('title', 'Pacientes — Saúde Guardiã')

@section('main')
    <div class="mx-auto max-w-7xl">
        {{-- Top Bar / Title --}}
        <div class="relative overflow-hidden rounded-2xl border border-[#e9e2d3] bg-gradient-to-tr from-[#fffaf3] via-white to-[#f7efe1] p-6 shadow-sm">
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-medium text-[#7f6b4d] ring-1 ring-[#eadfc9] backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M12 2a7 7 0 0 0-7 7v1.278A2 2 0 0 1 3.553 12l-.553 5a3 3 0 0 0 2.978 3.37H18.02A3 3 0 0 0 21 17l-.553-5A2 2 0 0 1 19 10.278V9a7 7 0 0 0-7-7Z"/>
                        </svg>
                        Saúde Guardiã — Pacientes
                    </div>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#2b2a27]">Pacientes</h1>
                    <p class="mt-1 text-sm text-[#7c7b78]">Gerencie as informações dos seus pacientes e acompanhe seus progressos.</p>
                </div>
                <div class="flex w-full flex-col items-stretch gap-3 sm:w-auto sm:flex-row">
                    <a
                        href="{{ route('pacientes.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#3b82f6] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/10 transition hover:translate-y-[-1px] hover:bg-[#2563eb] focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M12 4.5a.75.75 0 0 1 .75.75v6h6a.75.75 0 0 1 0 1.5h-6v6a.75.75 0 0 1-1.5 0v-6h-6a.75.75 0 0 1 0-1.5h6v-6A.75.75 0 0 1 12 4.5Z" clip-rule="evenodd" />
                        </svg>
                        Novo paciente
                    </a>
                </div>
            </div>

            {{-- Quick Filters & Search --}}
            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <form method="GET" action="{{ route('pacientes.index') }}" class="col-span-2">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nome, e‑mail ou telefone..."
                               class="w-full rounded-xl border border-[#eadfc9] bg-white/70 px-4 py-2.5 pr-10 text-sm text-[#2b2a27] shadow-sm placeholder:text-[#a8a093] focus:border-[#d6c6a6] focus:ring-2 focus:ring-[#d6c6a6]/60 backdrop-blur"/>
                        <span class="pointer-events-none absolute inset-y-0 right-3 inline-flex items-center text-[#a28f6c]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.042 12.18l4.264 4.264a.75.75 0 1 0 1.06-1.06l-4.264-4.265A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/></svg>
                        </span>
                    </div>
                </form>
                <form method="GET" action="{{ route('pacientes.index') }}" class="">
                    <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-[#eadfc9] bg-white/80 px-3 py-2.5 text-sm text-[#2b2a27] shadow-sm focus:border-[#d6c6a6] focus:ring-2 focus:ring-[#d6c6a6]/60 backdrop-blur">
                        <option value="">Todos os status</option>
                        <option value="ativo" @selected(request('status')==='ativo')>Ativo</option>
                        <option value="em_atendimento" @selected(request('status')==='em_atendimento')>Em atendimento</option>
                        <option value="inativo" @selected(request('status')==='inativo')>Inativo</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Success alert --}}
        @if (session('success'))
            <div class="mt-6 overflow-hidden rounded-xl border border-green-200/70 bg-green-50 p-4 text-sm text-green-900 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-2.59a.75.75 0 1 0-1.22-.87l-3.236 4.541-1.764-1.764a.75.75 0 0 0-1.06 1.06l2.5 2.5a.75.75 0 0 0 1.147-.089l3.633-5.378Z" clip-rule="evenodd" />
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        {{-- Card / Table --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-[#e9e2d3] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#fbf7ee] text-[#6f5f43]">
                        <tr class="border-b border-[#efe6d6]">
                            <th class="px-6 py-3 font-semibold">Paciente</th>
                            <th class="px-6 py-3 font-semibold">Plano</th>
                            <th class="px-6 py-3 font-semibold">Desde</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1eadf]">
                        @forelse ($pacientes as $paciente)
                            <tr class="hover:bg-[#fffaf3]">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar (iniciais) --}}
                                        @php
                                            $iniciais = collect(explode(' ', trim($paciente->nome)))->filter()->take(2)->map(fn($p) => mb_substr($p,0,1))->join('');
                                        @endphp
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#f3e7d3] to-[#e4d2b3] font-semibold text-[#5a4a2f] ring-1 ring-[#e1d4ba]">
                                            {{ $iniciais ?: 'PG' }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-[#2b2a27]">{{ $paciente->nome }}</div>
                                            <div class="text-xs text-[#8a7d63]">
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
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[#5b5955]">{{ $paciente->plano ?? '—' }}</td>
                                <td class="px-6 py-4 text-[#5b5955]">{{ $paciente->data_inicio?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $paciente->status;
                                        $styles = [
                                            'ativo' => 'bg-green-100 text-green-800 ring-green-200',
                                            'inativo' => 'bg-red-100 text-red-800 ring-red-200',
                                            'em_atendimento' => 'bg-amber-100 text-amber-800 ring-amber-200',
                                        ];
                                        $icons = [
                                            'ativo' => '<path d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-2.59a.75.75 0 1 0-1.22-.87l-3.236 4.541-1.764-1.764a.75.75 0 0 0-1.06 1.06l2.5 2.5a.75.75 0 0 0 1.147-.089l3.633-5.378Z"/>',
                                            'inativo' => '<path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm-3.53 6.47a.75.75 0 0 1 1.06 0L12 10.19l2.47-2.47a.75.75 0 0 1 1.06 1.06L13.06 11.25l2.47 2.47a.75.75 0 1 1-1.06 1.06L12 12.31l-2.47 2.47a.75.75 0 0 1-1.06-1.06l2.47-2.47-2.47-2.47a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>',
                                            'em_atendimento' => '<path d="M12 2.25A9.75 9.75 0 1 0 21.75 12 9.762 9.762 0 0 0 12 2.25Zm.75 4.5a.75.75 0 0 0-1.5 0v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 0-1.5h-2.25V6.75Z"/>',
                                        ];
                                        $cls = $styles[$status] ?? 'bg-green-100 text-green-800 ring-green-200';
                                        $icon = $icons[$status] ?? $icons['ativo'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $cls }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">{!! $icon !!}</svg>
                                        {{ str_replace('_', ' ', $status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('pacientes.dashboard', $paciente) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-200 hover:bg-indigo-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M3 4.5A1.5 1.5 0 0 1 4.5 3h15A1.5 1.5 0 0 1 21 4.5V6H3V4.5ZM3 7.5h18V19.5A1.5 1.5 0 0 1 19.5 21h-15A1.5 1.5 0 0 1 3 19.5V7.5Zm4.5 3.75A1.125 1.125 0 1 0 6.375 12 1.125 1.125 0 0 0 7.5 11.25Zm0 3A1.125 1.125 0 1 0 6.375 15 1.125 1.125 0 0 0 7.5 14.25Zm0 3A1.125 1.125 0 1 0 6.375 18 1.125 1.125 0 0 0 7.5 17.25Zm4.5-6h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5Zm0 3h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5Zm0 3h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5Z"/></svg>
                                            Dashboard
                                        </a>
                                        <a href="{{ route('pacientes.edit', $paciente) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-blue-700 ring-1 ring-blue-200 hover:bg-blue-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0L4.5 15.788V19.5h3.712L21.731 5.981a2.625 2.625 0 0 0 0-3.712Z"/><path d="M3.75 21h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1 0-1.5Z"/></svg>
                                            Editar
                                        </a>
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja remover este paciente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M9 3.75A1.5 1.5 0 0 1 10.5 2.25h3A1.5 1.5 0 0 1 15 3.75H19.5a.75.75 0 0 1 0 1.5H18v13.5A2.25 2.25 0 0 1 15.75 21H8.25A2.25 2.25 0 0 1 6 18.75V5.25H4.5a.75.75 0 0 1 0-1.5H9Zm-.75 3v12a.75.75 0 0 0 .75.75h7.5a.75.75 0 0 0 .75-.75v-12H8.25ZM10.5 7.5a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-1.5 0V8.25a.75.75 0 0 1 .75-.75Zm4.5 0a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-1.5 0V8.25a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/></svg>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16">
                                    <div class="flex flex-col items-center justify-center gap-3 text-center">
                                        <div class="rounded-2xl border border-dashed border-[#e9e2d3] bg-[#fffaf3] p-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-10 w-10 text-[#a28f6c]"><path fill-rule="evenodd" d="M12 4.5a.75.75 0 0 1 .75.75v6h6a.75.75 0 0 1 0 1.5h-6v6a.75.75 0 0 1-1.5 0v-6h-6a.75.75 0 0 1 0-1.5h6v-6A.75.75 0 0 1 12 4.5Z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-[#2b2a27]">Nenhum paciente ainda</h3>
                                        <p class="max-w-sm text-sm text-[#8a7d63]">Cadastre seu primeiro paciente para começar a acompanhar consultas, planos e evolução clínica.</p>
                                        <a href="{{ route('pacientes.create') }}" class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-[#3b82f6] px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-500/10 hover:bg-[#2563eb]">Adicionar paciente</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            <div class="flex items-center justify-between gap-4 border-t border-[#efe6d6] bg-[#fffdf8] px-4 py-3">
                <div class="text-xs text-[#8a7d63]">
                    @php
                        $from = ($pacientes->currentPage() - 1) * $pacientes->perPage() + 1;
                        $to = min($pacientes->currentPage() * $pacientes->perPage(), $pacientes->total());
                    @endphp
                    Mostrando <span class="font-semibold text-[#5a4a2f]">{{ $pacientes->count() ? $from : 0 }}–{{ $pacientes->count() ? $to : 0 }}</span> de <span class="font-semibold text-[#5a4a2f]">{{ $pacientes->total() }}</span>
                </div>
                <div class="[&>nav>div>span]:rounded-lg [&>nav>div>span]:px-3 [&>nav>div>span]:py-2 [&>nav>div>span]:ring-1 [&>nav>div>span]:ring-[#eadfc9] [&>nav>div>span]:bg-white [&>nav>div>span]:text-[#6f5f43] [&>nav>div>a]:rounded-lg [&>nav>div>a]:px-3 [&>nav>div>a]:py-2 [&>nav>div>a]:ring-1 [&>nav>div>a]:ring-[#eadfc9] [&>nav>div>a]:bg-white hover:[&>nav>div>a]:bg-[#fff6e6]">
                    {{ $pacientes->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection