@extends('layouts.app')

@section('title', 'Pacientes — Saúde Guardiã')

@section('main')
    {{-- Cabeçalho --}}
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 border border-indigo-100">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-700">
                    Saúde Guardiã &middot; Painel
                </span>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
                    Pacientes
                </h1>
                <p class="text-sm md:text-base text-slate-500 mt-1">
                    Gerencie as informações dos seus pacientes e acompanhe seus progressos em um só lugar.
                </p>
            </div>
        </div>

        <a
            href="{{ route('pacientes.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white
                   shadow-lg shadow-indigo-400/40 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2
                   focus-visible:ring-indigo-500 focus-visible:ring-offset-2 transition-all"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                <path d="M12 5V19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                <path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            Novo paciente
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-100">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                </svg>
            </div>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Card principal --}}
    <div class="rounded-3xl border border-[#e3d7c3]/80 bg-gradient-to-b from-[#f8f3e9] via-white to-white px-4 py-5 md:px-6 md:py-7 shadow-[0_18px_40px_rgba(15,23,42,0.08)]">
        {{-- Barra superior do card (resumo / paginação) --}}
        <div class="mb-4 flex flex-col gap-2 md:mb-6 md:flex-row md:items-center md:justify-between">
            <div class="text-xs text-slate-500">
                @php
                    $total = $pacientes->total();
                    $count = $pacientes->count();
                @endphp
                Mostrando
                <span class="font-semibold text-slate-700">{{ $count }}</span>
                de
                <span class="font-semibold text-slate-700">{{ $total }}</span>
                paciente{{ $total === 1 ? '' : 's' }}.
            </div>
            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Lista atualizada em tempo real
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-[#e3d7c3] bg-white/90">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#f3ede1]">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">
                            Paciente
                        </th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">
                            Plano
                        </th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">
                            Desde
                        </th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f3ede1]/80">
                    @forelse ($pacientes as $paciente)
                        <tr class="hover:bg-slate-50/80 transition">
                            {{-- Paciente --}}
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-[13px] font-semibold text-indigo-700">
                                            {{ mb_strtoupper(mb_substr($paciente->nome, 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="font-semibold text-slate-900">
                                                {{ $paciente->nome }}
                                            </div>
                                            <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                                @if ($paciente->email)
                                                    <span>{{ $paciente->email }}</span>
                                                @endif
                                                @if ($paciente->email && $paciente->telefone)
                                                    <span class="text-[9px]">•</span>
                                                @endif
                                                @if ($paciente->telefone)
                                                    <span>{{ $paciente->telefone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Plano --}}
                            <td class="px-6 py-4 align-top text-sm text-slate-600">
                                @if ($paciente->plano)
                                    <span class="inline-flex rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                                        {{ $paciente->plano }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Desde --}}
                            <td class="px-6 py-4 align-top text-sm text-slate-600">
                                {{ $paciente->data_inicio?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 align-top text-sm">
                                @php
                                    $status = $paciente->status;
                                    $statusClasses = [
                                        'ativo' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                        'inativo' => 'bg-rose-50 text-rose-700 ring-rose-100',
                                        'em_atendimento' => 'bg-amber-50 text-amber-700 ring-amber-100',
                                    ];
                                    $badgeClass = $statusClasses[$status] ?? 'bg-emerald-50 text-emerald-700 ring-emerald-100';
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize ring-1 {{ $badgeClass }}"
                                >
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current/60"></span>
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 align-top text-right text-sm">
                                <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                                    {{-- Dashboard --}}
                                    <a
                                        href="{{ route('pacientes.dashboard', $paciente) }}"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700
                                               hover:bg-indigo-100 hover:border-indigo-200 transition"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                            <path d="M4 13V20H10V13H4Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14 4V20H20V4H14Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Dashboard
                                    </a>

                                    {{-- Editar --}}
                                    <a
                                        href="{{ route('pacientes.edit', $paciente) }}"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700
                                               hover:border-slate-300 hover:bg-slate-50 transition"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 19L5.5 16L15.5 6L18 8.5L8 18.5L5 19Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Editar
                                    </a>

                                    {{-- Enviar acompanhamento --}}
<form
    action="{{ route('pacientes.enviar-acompanhamento', $paciente) }}"
    method="POST"
    class="inline-flex"
    id="enviar-acompanhamento-{{ $paciente->id }}"
>
    @csrf
    @php
        $canSend = (bool) ($paciente->whatsapp_numero || $paciente->telefone);
    @endphp
    <input type="hidden" name="consideracoes" value="">
    <button
        type="button"
        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm
               hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500
               disabled:cursor-not-allowed disabled:opacity-60 transition"
        data-open-consideracoes
        data-target-form="enviar-acompanhamento-{{ $paciente->id }}"
        @unless($canSend) disabled title="Cadastre um número de WhatsApp para enviar o acompanhamento." @endunless
    >
        {{-- Ícone WhatsApp --}}
        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.198.297-.768.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.007-.372-.009-.57-.009-.198 0-.52.074-.792.372-.272.298-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.29.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12.004 2.003c-5.495 0-9.997 4.502-9.997 9.997 0 1.762.463 3.487 1.34 4.997L2 22l5.158-1.327c1.46.795 3.11 1.218 4.846 1.218h.003c5.495 0 9.997-4.502 9.997-9.997 0-2.67-1.04-5.177-2.927-7.064C17.181 3.043 14.673 2.003 12.004 2.003zm0 18.13h-.002c-1.52 0-3.003-.402-4.302-1.164l-.308-.182-3.065.788.817-2.987-.198-.308a8.167 8.167 0 01-1.267-4.418c0-4.522 3.68-8.202 8.202-8.202 2.19 0 4.257.853 5.81 2.406 1.553 1.553 2.406 3.62 2.406 5.81 0 4.522-3.68 8.202-8.203 8.202z"/>
        </svg>

        Enviar
    </button>
</form>


                                    {{-- Excluir --}}
                                    <form
                                        action="{{ route('pacientes.destroy', $paciente) }}"
                                        method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Tem certeza que deseja remover este paciente?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition"
                                        >
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                <path d="M6 7H18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                                <path d="M10 10V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                                <path d="M14 10V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                                <path d="M9 7L9.5 5H14.5L15 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M7 7L7.5 19H16.5L17 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                Nenhum paciente cadastrado ainda.
                                <a href="{{ route('pacientes.create') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                                    Cadastre o primeiro paciente.
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $pacientes->links() }}
        </div>
    </div>

    @include('partials.consideracoes_modal')
@endsection
