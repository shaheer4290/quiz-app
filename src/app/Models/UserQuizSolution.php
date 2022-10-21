<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizSolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_quiz_id',
        'question_id',
        'answers',
        'score',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function Question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
