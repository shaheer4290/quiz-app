<?php

namespace App\Services;

interface UserService
{
    public function solveQuiz($request, $quiz);

    public function getMyQuizzes();

    public function getAllQuizResult();

    public function getQuizResult($quiz);

    public function getOtherUsersQuizResult();
}
