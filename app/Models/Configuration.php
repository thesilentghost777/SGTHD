<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_config',
        'flag1',
        'flag2',
        'flag3',
        'flag4',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_config' => 'boolean',
        'flag1' => 'boolean',
        'flag2' => 'boolean',
        'flag3' => 'boolean',
        'flag4' => 'boolean',
    ];
}
