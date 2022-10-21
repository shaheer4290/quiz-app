<?php

namespace App\Repositories;

use App\Models\QuizQuestion;

class QuizQuestionRepositoryImpl implements QuizQuestionRepository
{
    public function create($data)
    {
        $question = QuizQuestion::create($data);

        return $question;
    }

    public function update($id, $questionData)
    {
        QuizQuestion::find($id)->update($questionData);
    }

    public function delete($quizQuestion)
    {
        return $quizQuestion->delete();
    }
}
