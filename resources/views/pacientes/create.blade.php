@extends('layouts.app')

@section('title', 'Novo paciente — Saúde Guardiã')

@section('main')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Novo paciente</h1>
            <p class="text-gray-500">Cadastre um novo paciente e defina informações essenciais para o acompanhamento.</p>
        </div>

        <form action="{{ route('pacientes.store') }}" method="POST" class="space-y-8">
            @include('pacientes.form', ['paciente' => null, 'metas' => $metas ?? collect()])
        </form>
    </div>
@endsection
