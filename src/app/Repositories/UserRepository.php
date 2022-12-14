<?php

namespace App\Repositories;

interface UserRepository
{
    public function createUser($request);

    public function getLoggedInUser();

    public function getUsersForEmail($id);
}
