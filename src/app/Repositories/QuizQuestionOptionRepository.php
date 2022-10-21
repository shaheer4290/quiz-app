<?php

namespace App\Repositories;

interface QuizQuestionOptionRepository
{
    public function create($data);

    public function update($data, $questionOption);

    public function delete($questionOption);
}
