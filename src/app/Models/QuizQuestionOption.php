<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'option',
        'question_id',
        'is_correct',
    ];

    public function Question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
