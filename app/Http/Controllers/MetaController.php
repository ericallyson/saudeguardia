<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Services\MetaMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaController extends Controller
{
    public function __construct(
        private readonly MetaMessageService $metaMessageService
    ) {
    }

    /**
     * Display a listing of the metas.
     */
    public function index(): View
    {
        $metas = Meta::query()
            ->where(function ($query): void {
                $query->where('user_id', auth()->id())
                    ->orWhereNull('user_id');
            })
            ->latest()
            ->paginate(10);

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
        $data['user_id'] = $request->user()->id;

        Meta::create($data);

        return redirect()->route('metas.index')->with('success', 'Meta criada com sucesso.');
    }

    /**
     * Show the form for editing the specified meta.
     */
    public function edit(Meta $meta): View
    {
        $this->ensureMetaVisibleToUser($meta);

        $tipos = Meta::TIPOS;
        $periodicidades = Meta::PERIODICIDADES;

        return view('metas.edit', compact('meta', 'tipos', 'periodicidades'));
    }

    /**
     * Update the specified meta in storage.
     */
    public function update(Request $request, Meta $meta): RedirectResponse
    {
        $this->ensureMetaVisibleToUser($meta);

        $data = $this->validateMeta($request);

        $meta->update($data);

        $this->metaMessageService->rebuildForMeta($meta);

        return redirect()->route('metas.index')->with('success', 'Meta atualizada com sucesso.');
    }

    /**
     * Remove the specified meta from storage.
     */
    public function destroy(Meta $meta): RedirectResponse
    {
        $this->ensureMetaVisibleToUser($meta);

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

    private function ensureMetaVisibleToUser(Meta $meta): void
    {
        if ($meta->user_id !== null && (int) $meta->user_id !== (int) auth()->id()) {
            abort(404);
        }
    }
}
