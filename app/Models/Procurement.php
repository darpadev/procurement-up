<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'payment',
        'applicant',
        'origin',
        'pic',
        'disposition',
        'dispo_date',
        'finish_target',
        'completion_date',
        'status',
    ];
}
