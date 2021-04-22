<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement', 'name', 'specs', 'price', 'qty'
    ];
}
