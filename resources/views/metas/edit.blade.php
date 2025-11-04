@extends('layouts.app')

@section('title', 'Editar meta — Saúde Guardiã')

@section('main')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Editar meta</h1>
            <p class="text-gray-500">Atualize as informações da meta selecionada.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('metas.update', $meta) }}" method="POST">
                @method('PUT')
                @include('metas.form', ['meta' => $meta, 'periodicidades' => $periodicidades])
            </form>
        </div>
    </div>
@endsection
