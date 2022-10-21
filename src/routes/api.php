<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//  Authentication Routes //

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

// Routes For Quiz Management //
Route::controller(QuizController::class)->group(function () {
    // CRUD operation for quizzes
    Route::get('/quizzes', 'getAll');

    Route::get('/quizzes/{quiz}', 'get')->where(['quiz' => '[A-Za-z0-9-]+']);

    Route::post('/quizzes', 'create');

    Route::put('/quizzes/{quiz}', 'update')->where(['quiz' => '[A-Za-z0-9-]+']);

    Route::delete('/quizzes/{quiz}', 'destroy')->where(['quiz' => '[A-Za-z0-9-]+']);

    Route::post('/quizzes/{quiz}/publish', 'publish')->where(['quiz' => '[A-Za-z0-9-]+']);
    // CRUD operation for question and options
    Route::get('/quizzes/{quiz}/questions/{question}', 'getQuestion')->where(['quiz' => '[A-Za-z0-9-]+', 'question' => '[0-9]+']);

    Route::post('/quizzes/{quiz}/questions/', 'addQuestions')->where(['quiz' => '[A-Za-z0-9-]+']);

    Route::put('/quizzes/{quiz}/questions/{question}', 'updateQuestion')->where(['quiz' => '[A-Za-z0-9-]+', 'question' => '[0-9]+']);

    Route::delete('/quizzes/{quiz}/questions/{question}', 'deleteQuestion')->where(['quiz' => '[A-Za-z0-9-]+', 'question' => '[0-9]+']);
    Route::delete('/quizzes/{quiz}/questions/{question}/options/{questionOption}', 'deleteQuestionOption')->where(['quiz' => '[A-Za-z0-9-]+', 'question' => '[0-9]+', 'questionOption' => '[0-9]+']);
});

// Routes For User Quiz Management //
Route::controller(UserController::class)->group(function () {
    // CRUD operation for quizzes
    Route::post('/users/quizzes/{quiz}/attempt', 'solveQuiz')->where(['quiz' => '[A-Za-z0-9-]+']);
    Route::get('/users/my-quizzes', 'getMyQuizzes');
    Route::get('/users/quizzes', 'getAllQuizzes');
    Route::get('/users/quizzes/stats', 'getOtherUsersResult');
    Route::get('/users/quizzes/{quiz}', 'getQuizResult')->where(['quiz' => '[A-Za-z0-9-]+']);
});
