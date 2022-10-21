<?php

namespace App\Repositories;

interface QuizQuestionRepository
{
    public function create($data);

    public function update($data, $quizQuestion);

    public function delete($quizQuestion);
}
