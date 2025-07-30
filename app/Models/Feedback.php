<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'from',
        'about',
        'course',
        'rate',
        'content',
        'date'
    ];
    
    public function receiver() {
        return $this->belongsTo(User::class, 'about');
    }

    public function sender() {
        return $this->belongsTo(User::class, 'from');
    }
}

// Congratulations Wilma Reichel, you are the greatest score between your friends in machine learining II so far, keep the hard work going

// you are at risk of fail if you keeping that performance, you should consider working hrad from now before it's too late, thank you

// good work Malvina Bailey, you should focus on the second chapter more
