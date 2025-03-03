<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
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

    public function professor() {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    public function grades() {
        return $this->hasMany(Grade::class, 'course_id');
    }

    public function attendance() {
        return $this->hasMany(Attendance::class, 'course_id');
    }

    public function assignments() {
        return $this->hasMany(Assignment::class, 'course_id');
    }
}
