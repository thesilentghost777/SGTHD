<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'montant',
        'personnalise',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
