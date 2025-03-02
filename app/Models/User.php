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
        'gpa',
        'major'
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
            'gpa' => 'decimal:3',
        ];
    }

    public function parent() {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(User::class, 'parent_id')->where('role', 'student');
    }
}
