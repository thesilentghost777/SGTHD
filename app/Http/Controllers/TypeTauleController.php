<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeTaule;
use Illuminate\Support\Facades\Validator;

class TypeTauleController extends Controller
{
    public function index()
    {
        $typesTaules = TypeTaule::all();
        return view('taules.types.index', compact('typesTaules'));
    }

    public function create()
    {
        return view('taules.types.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formule_farine' => 'nullable|string',
            'formule_eau' => 'nullable|string',
            'formule_huile' => 'nullable|string',
            'formule_autres' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeTaule::create($request->all());

        return redirect()->route('taules.types.index')
            ->with('success', 'Type de taule créé avec succès.');
    }

    public function edit(TypeTaule $type)
    {
        return view('taules.types.edit', compact('type'));
    }

    public function update(Request $request, TypeTaule $type)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formule_farine' => 'nullable|string',
            'formule_eau' => 'nullable|string',
            'formule_huile' => 'nullable|string',
            'formule_autres' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $type->update($request->all());

        return redirect()->route('taules.types.index')
            ->with('success', 'Type de taule mis à jour avec succès.');
    }

    public function destroy(TypeTaule $type)
    {
        $type->delete();

        return redirect()->route('taules.types.index')
            ->with('success', 'Type de taule supprimé avec succès.');
    }
}
