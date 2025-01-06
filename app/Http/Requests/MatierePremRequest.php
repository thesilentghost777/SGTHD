<?php

namespace App\Http\Requests;

use App\Enums\UniteMinimale;
use Illuminate\Foundation\Http\FormRequest;

class MatierePremRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'unite_minimale' => 'required|in:' . implode(',', UniteMinimale::values()),
            'unite_classique' => 'required|string|max:50',
            'quantite_par_unite' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'unite_minimale.in' => 'L\'unité minimale doit être l\'une des suivantes: ' . implode(', ', UniteMinimale::values()),
        ];
    }
}
