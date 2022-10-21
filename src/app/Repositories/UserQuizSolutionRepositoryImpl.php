<?php

namespace App\Repositories;

use App\Models\UserQuizSolution;

class UserQuizSolutionRepositoryImpl implements UserQuizSolutionRepository
{
    public function addUserSolution($data)
    {
        return UserQuizSolution::create($data);
    }
}
