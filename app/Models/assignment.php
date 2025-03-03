<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'score',
        'status',
        'course_id',
        'professor_id',
    ];

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function professor() {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
