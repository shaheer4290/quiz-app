<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    public const SINGLE_CORRECT_ANSWER = 'single';

    public const MULTIPLE_CORRECT_ANSWER = 'multiple';

    public const MAXIMUM_OPTIONS_COUNT = 5;

    protected $fillable = [
        'quiz_id',
        'question',
        'correct_answer',
    ];

    public function Options()
    {
        return $this->hasMany(QuizQuestionOption::class, 'question_id');
    }

    public function Quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}
