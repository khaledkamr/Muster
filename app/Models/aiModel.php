<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aiModel extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description'
    ];

    public function trainingHistories() {
        return $this->hasMany(TrainingHistory::class);
    }
}
