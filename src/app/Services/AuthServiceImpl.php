<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthServiceImpl implements AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register($request)
    {
        $success = $this->userRepository->createUser($request);

        return $success;
    }

    public function login($request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid login Credentials');
        }

        $accessToken = Auth::attempt($credentials);

        if (! $accessToken) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unable to login, Unauthorized');
        }

        $user = Auth::user();
        $user->accessToken = $accessToken;

        return $user;
    }

    public function logout()
    {
        Auth::logout();
    }

    public function refreshToken()
    {
        $user = Auth::user();
        $user->accessToken = Auth::refresh();

        return $user;
    }
}
