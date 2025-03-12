<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'status',
        'student_id',
        'course_id',
        'type',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
