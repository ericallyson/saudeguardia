<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaController extends Controller
{
    /**
     * Display a listing of the metas.
     */
    public function index(): View
    {
        $metas = Meta::latest()->paginate(10);

        return view('metas.index', compact('metas'));
    }

    /**
     * Show the form for creating a new meta.
     */
    public function create(): View
    {
        $tipos = Meta::TIPOS;
        $periodicidades = Meta::PERIODICIDADES;

        return view('metas.create', compact('tipos', 'periodicidades'));
    }

    /**
     * Store a newly created meta in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMeta($request);

        Meta::create($data);

        return redirect()->route('metas.index')->with('success', 'Meta criada com sucesso.');
    }

    /**
     * Show the form for editing the specified meta.
     */
    public function edit(Meta $meta): View
    {
        $tipos = Meta::TIPOS;
        $periodicidades = Meta::PERIODICIDADES;

        return view('metas.edit', compact('meta', 'tipos', 'periodicidades'));
    }

    /**
     * Update the specified meta in storage.
     */
    public function update(Request $request, Meta $meta): RedirectResponse
    {
        $data = $this->validateMeta($request);

        $meta->update($data);

        return redirect()->route('metas.index')->with('success', 'Meta atualizada com sucesso.');
    }

    /**
     * Remove the specified meta from storage.
     */
    public function destroy(Meta $meta): RedirectResponse
    {
        $meta->delete();

        return redirect()->route('metas.index')->with('success', 'Meta removida com sucesso.');
    }

    /**
     * Validate the meta request data.
     */
    protected function validateMeta(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'tipo' => ['required', 'in:' . implode(',', array_keys(Meta::TIPOS))],
            'periodicidade_padrao' => ['required', 'in:' . implode(',', array_keys(Meta::PERIODICIDADES))],
        ]);
    }
}
