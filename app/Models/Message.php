<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table='Message';
    protected $fillable = ['message', 'type', 'date_message', 'name', 'read'];
}
