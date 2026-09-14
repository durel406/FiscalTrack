<?php

namespace App\Http\Controllers;

use App\Models\Contribuable;
use Illuminate\Http\Request;

class ContribuableController extends Controller
{
    public function index()
    {
        // On passe les contribuables déjà existants en base, pour que la page
        // n'affiche pas un tableau vide tant que le JS n'a pas fait de fetch().
        return view('contribuables.index', [
            'initialContribuables' => Contribuable::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        return Contribuable::create($data);
    }

    public function update(Request $request, Contribuable $contribuable)
    {
        $data = $this->validated($request);
        $contribuable->update($data);
        return $contribuable;
    }

    public function destroy(Contribuable $contribuable)
    {
        $contribuable->delete();
        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nom' => 'required|string|max:255',
            'niu' => 'nullable|string|max:50',
            'regime' => 'nullable|string|max:100',
            'cat' => 'nullable|string|max:100',
            'pass' => 'nullable|string',
            'montant' => 'nullable|integer|min:0',
            't1' => 'nullable|integer|min:0', 't2' => 'nullable|integer|min:0',
            't3' => 'nullable|integer|min:0', 't4' => 'nullable|integer|min:0', 'tdl' => 'nullable|integer|min:0',
            'impots' => 'nullable|integer|min:0', 'loyer' => 'nullable|integer|min:0', 'bail' => 'nullable|integer|min:0',
            'precompte' => 'nullable|integer|min:0', 'timbre' => 'nullable|integer|min:0',
            'frais_paiement' => 'nullable|integer|min:0',
            'fs_paye' => 'nullable|integer|min:0', 'fs_non_paye' => 'nullable|integer|min:0',
            'ai_igs' => 'nullable|date', 'ai_bail' => 'nullable|date', 'ai_precompte' => 'nullable|date',
            'q_igs' => 'nullable|date', 'q_bail' => 'nullable|date', 'q_precompte' => 'nullable|date',
            'acf_igs' => 'nullable|date', 'acf_bail' => 'nullable|date', 'acf_precompte' => 'nullable|date',
            'lieu' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:30',
        ]);
    }
}
