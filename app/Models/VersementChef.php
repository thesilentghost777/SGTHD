<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class VersementChef extends Model
{
    use HasFactory;
    protected $primaryKey='code_vc';
    protected $fillable = [
        'chef_production',
        'libelle',
        'montant',
        'created_at',
        'updated_at'
    ];
}
