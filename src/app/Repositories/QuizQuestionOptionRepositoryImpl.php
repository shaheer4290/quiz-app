<?php

namespace App\Repositories;

use App\Models\QuizQuestionOption;

class QuizQuestionOptionRepositoryImpl implements QuizQuestionOptionRepository
{
    public function create($data)
    {
        $questionOption = QuizQuestionOption::create($data);
    }

    public function update($id, $questionOptionData)
    {
        QuizQuestionOption::find($id)->update($questionOptionData);
    }

    public function delete($questionOption)
    {
        return $questionOption->delete();
    }
}
