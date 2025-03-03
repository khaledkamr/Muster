<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'department',
        'credit_hours',
        'semester',
        'type',
        'difficulty',
        'professor_id',
    ];
}
