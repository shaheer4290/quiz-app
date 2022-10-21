<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolveQuizRequest;
use App\Http\Resources\MyQuizListCollection;
use App\Http\Resources\OtherUserQuizResultCollection;
use App\Http\Resources\UserQuizResultCollection;
use App\Http\Resources\UserQuizResultResource;
use App\Models\Quiz;
use App\Services\UserService;
use App\Utils\ResponseUtils;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api');
        $this->userService = $userService;
    }

    public function solveQuiz(SolveQuizRequest $request, Quiz $quiz)
    {
        if (! $quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('Quiz is not published yet. So submissions are not allowed', Response::HTTP_FORBIDDEN);
        }

        if (Auth::user()->quizesTaken->contains($quiz)) {
            return ResponseUtils::sendResponseWithError('You have already taken the quiz', Response::HTTP_FORBIDDEN);
        }

        $this->userService->solveQuiz($request, $quiz);

        return ResponseUtils::sendResponseWithoutData('Quiz submitted successfully', Response::HTTP_OK);
    }

    public function getQuizResult(Quiz $quiz)
    {
        if (! Auth::user()->quizesTaken->contains($quiz)) {
            return ResponseUtils::sendResponseWithError('You have not taken the quiz yet', Response::HTTP_FORBIDDEN);
        }

        $userQuiz = $this->userService->getQuizResult($quiz);

        return ResponseUtils::sendResponseWithSuccess('Quiz Result', new UserQuizResultResource($userQuiz), Response::HTTP_OK);
    }

    public function getMyQuizzes()
    {
        $myQuizzes = $this->userService->getMyQuizzes();

        return new MyQuizListCollection($myQuizzes);
    }

    public function getAllQuizzes()
    {
        $userQuizzes = $this->userService->getAllQuizResult();

        return new UserQuizResultCollection($userQuizzes);
    }

    public function getOtherUsersResult()
    {
        $otherUserQuizzes = $this->userService->getOtherUsersQuizResult();

        return new OtherUserQuizResultCollection($otherUserQuizzes);
    }
}
