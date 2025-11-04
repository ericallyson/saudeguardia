<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index(): View
    {
        $pacientes = Paciente::latest()->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(): View
    {
        return view('pacientes.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePaciente($request);

        Paciente::create($data);

        return redirect()->route('pacientes.index')->with('success', 'Paciente cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Paciente $paciente): View
    {
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $data = $this->validatePaciente($request);

        $paciente->update($data);

        return redirect()->route('pacientes.index')->with('success', 'Paciente atualizado com sucesso.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Paciente $paciente): RedirectResponse
    {
        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente removido com sucesso.');
    }

    /**
     * Validate patient data.
     */
    protected function validatePaciente(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'data_nascimento' => ['nullable', 'date'],
            'plano' => ['nullable', 'string', 'max:255'],
            'data_inicio' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'peso_inicial' => ['nullable', 'numeric', 'min:0'],
            'altura_cm' => ['nullable', 'integer', 'min:0'],
            'circunferencia_abdominal' => ['nullable', 'numeric', 'min:0'],
            'condicoes_medicas' => ['nullable', 'string'],
            'peso_meta' => ['nullable', 'numeric', 'min:0'],
            'prazo_meses' => ['nullable', 'integer', 'min:0'],
            'atividade_fisica' => ['nullable', 'string', 'max:255'],
            'whatsapp_numero' => ['nullable', 'string', 'max:30'],
            'whatsapp_frequencia' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
