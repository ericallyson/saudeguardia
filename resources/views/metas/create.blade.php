@extends('layouts.app')

@section('title', 'Nova meta — Saúde Guardiã')

@section('main')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Nova meta</h1>
            <p class="text-gray-500">Cadastre uma nova meta para acompanhamento dos pacientes.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('metas.store') }}" method="POST">
                @include('metas.form', ['meta' => null])
            </form>
        </div>
    </div>
@endsection
