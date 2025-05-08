<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'department', 
        'phone', 
        'birth_date', 
        'gender', 
        'year', 
        'major',
        'credit_hours',
        'parent_id',
    ];

    public function isProfessor() {
        return $this->role === 'professor';
    }

    public function isStudent() {
        return $this->role === 'student';
    }

    public function isParent() {
        return $this->role === 'parent';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function parent() {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(User::class, 'parent_id')->where('role', 'student');
    }

    public function courses() { // for professors
        return $this->hasMany(Course::class, 'professor_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function grades() {
        return $this->hasMany(Grade::class, 'student_id');
    }
    
    public function attendance() {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function assignments() {
        return $this->hasMany(Assignment::class, 'professor_id');
    }

    public function assignmentSubmissions() {
        return $this->hasMany(Assignment_submission::class, 'student_id');
    }

    public function completedCreditHours() {
        return $this->grades()->where('status', 'pass')->with('course')->sum('course.credit_hours');
    }
}
    
