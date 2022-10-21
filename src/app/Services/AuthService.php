<?php

namespace App\Services;

interface AuthService
{
    public function register($request);

    public function login($request);

    public function logout();

    public function refreshToken();
}
