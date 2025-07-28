<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ai_model_id',
        'accuracy',
        'loss',
        'epochs',
        'date',
        'execution_time'
    ];

    public function aiModel() {
        return $this->belongsTo(aiModel::class);
    }
}
