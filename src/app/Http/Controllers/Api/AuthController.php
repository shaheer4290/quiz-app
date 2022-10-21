<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Utils\ResponseUtils;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService;
    }

    /*
    Login using email and password
     */
    public function login(LoginRequest $request)
    {
        if (Auth::check()) {
            return ResponseUtils::sendResponseWithError('You are already logged', Response::HTTP_FORBIDDEN);
        }

        $user = $this->authService->login($request);

        return ResponseUtils::sendResponseWithSuccess('User Successfully logged in', new  UserResource($user), Response::HTTP_OK);
    }

    /*
    Register User into application
     */
    public function register(RegisterRequest $request)
    {
        $success = $this->authService->register($request);

        if ($success) {
            return ResponseUtils::sendResponseWithoutData('User Successfully registered', Response::HTTP_CREATED);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to Register', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
    Logout of thr application
     */
    public function logout()
    {
        $this->authService->logout();

        return ResponseUtils::sendResponseWithoutData('User Successfully logged out', Response::HTTP_OK);
    }

    public function me()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
        ]);
    }

    public function refresh()
    {
        $user = $this->authService->refreshToken();

        return ResponseUtils::sendResponseWithSuccess('Access Token Refreshed successfully', new  UserResource($user), Response::HTTP_OK);
    }
}
