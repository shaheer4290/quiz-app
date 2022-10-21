<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
    ];

    public function Solutions()
    {
        return $this->hasMany(UserQuizSolution::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
