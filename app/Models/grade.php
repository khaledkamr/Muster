<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'semester',
        'quiz1',
        'quiz2',
        'midterm',
        'project',
        'final',
        'total',
        'grade',
        'status',
    ];
}
