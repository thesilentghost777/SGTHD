<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUtilisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produit' => 'required|exists:Produit_fixes,code_produit',
            'quantite_produit' => 'required|numeric|min:0.01',
            'matieres' => 'required|array',
            'matieres.*.matiere_id' => 'required|exists:Matiere,id',
            'matieres.*.quantite' => 'required|numeric|min:0.001',
            'matieres.*.unite' => 'required|string',
        ];
    }
}
