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
        'course_id',
        'professor_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function professor() {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function submissions() {
        return $this->hasMany(Assignment_submission::class, 'assignment_id');
    }
}
