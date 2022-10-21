<?php

namespace App\Repositories;

interface UserQuizRepository
{
    public function addUserQuiz($data);

    public function getUserQuiz($user_id, $quiz_id);

    public function getAllUserQuizzes($user_id);

    public function getAllOtherUserQuizzes($user_id);
}
