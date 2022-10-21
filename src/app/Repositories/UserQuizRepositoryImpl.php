<?php

namespace App\Repositories;

use App\Models\UserQuiz;

class UserQuizRepositoryImpl implements UserQuizRepository
{
    public function addUserQuiz($data)
    {
        return UserQuiz::create($data);
    }

    public function getUserQuiz($user_id, $quiz_id)
    {
        return UserQuiz::where([
            ['user_id', '=', $user_id],
            ['quiz_id', '=', $quiz_id],
        ])->first();
    }

    public function getAllUserQuizzes($user_id)
    {
        return UserQuiz::where('user_id', $user_id)->paginate(10);
    }

    public function getAllOtherUserQuizzes($user_id)
    {
        return UserQuiz::with('solutions')
                        ->whereIn('quiz_id', function ($query) use ($user_id) {
                            $query->select('id')
                            ->from('quizzes')
                            ->where('created_by', $user_id);
                        })->distinct()
                        ->paginate(10);
    }
}
