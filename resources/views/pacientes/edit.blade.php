@extends('layouts.app')

@section('title', 'Editar paciente — Saúde Guardiã')

@section('main')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Editar paciente</h1>
            <p class="text-gray-500">Atualize as informações do paciente selecionado.</p>
        </div>

        <form action="{{ route('pacientes.update', $paciente) }}" method="POST" class="space-y-8">
            @method('PUT')
            @include('pacientes.form', ['paciente' => $paciente])
        </form>
    </div>
@endsection
