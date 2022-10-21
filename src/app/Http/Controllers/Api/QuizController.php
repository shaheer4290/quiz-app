<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateQuizQuestionsRequest;
use App\Http\Requests\CreateQuizRequest;
use App\Http\Requests\UpdateQuizQuestionRequest;
use App\Http\Resources\QuizListCollection;
use App\Http\Resources\QuizQuestionResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Services\QuizService;
use App\Utils\ResponseUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends Controller
{
    private QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->middleware('auth:api');
        $this->quizService = $quizService;
    }

    /*
    Get a quiz by slug
    */
    public function get(Quiz $quiz)
    {
        if (! $quiz->isPublished() && $quiz->created_by != Auth::user()->id) {
            return ResponseUtils::sendResponseWithError('Quiz Not Found', Response::HTTP_NOT_FOUND);
        }

        $quiz = $this->quizService->get($quiz);

        return ResponseUtils::sendResponseWithSuccess('Quiz found', $quiz, Response::HTTP_OK);
    }

    /*
    Get list of all published quizes
    */
    public function getAll()
    {
        $quizzes = $this->quizService->getAll();

        return new QuizListCollection($quizzes);
    }

    /*
    Create a new quiz
    */
    public function create(CreateQuizRequest $request)
    {
        $quiz = $this->quizService->add($request);

        if (! empty($quiz)) {
            return ResponseUtils::sendResponseWithSuccess('Quiz created successfully', new QuizResource($quiz), Response::HTTP_CREATED);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to create quiz', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
    Upadate an existing quiz
    */
    public function update(CreateQuizRequest $request, Quiz $quiz)
    {
        if (Gate::forUser(Auth::user())->denies('update-quiz', $quiz)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('Published quiz cannot be updated', Response::HTTP_FORBIDDEN);
        }

        $quiz = $this->quizService->update($request, $quiz);

        if (! empty($quiz)) {
            return ResponseUtils::sendResponseWithSuccess('Quiz updated successfully', new QuizResource($quiz), Response::HTTP_OK);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to update quiz', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
     Delete a quiz
    */
    public function destroy(Quiz $quiz)
    {
        if (Gate::forUser(Auth::user())->denies('delete-quiz', $quiz)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        $success = $this->quizService->delete($quiz);

        if ($success) {
            return ResponseUtils::sendResponseWithoutData('Quiz deleted successfully', Response::HTTP_OK);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to create quiz', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
    Publish a quiz
     */
    public function publish(Quiz $quiz)
    {
        if (Gate::forUser(Auth::user())->denies('publish-quiz', $quiz)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('Quiz already published', Response::HTTP_FORBIDDEN);
        }

        $success = $this->quizService->publish($quiz);

        if ($success) {
            return ResponseUtils::sendResponseWithoutData('Quiz successfully published', Response::HTTP_OK);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to publish quiz', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
    Get question details by question id
     */
    public function getQuestion(Quiz $quiz, QuizQuestion $question)
    {
        $question = $this->quizService->getQuestion($quiz, $question);

        return ResponseUtils::sendResponseWithSuccess('Question found', new QuizQuestionResource($question), Response::HTTP_OK);
    }

    /*
    Add questions to a quiz
     */
    public function addQuestions(CreateQuizQuestionsRequest $request, Quiz $quiz)
    {
        if (Gate::forUser(Auth::user())->denies('add-quiz-questions', $quiz)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('You cannot add questions to a published quiz', Response::HTTP_FORBIDDEN);
        }

        $quiz = $this->quizService->addQuestions($request, $quiz);

        return ResponseUtils::sendResponseWithSuccess('Quiz questions added successfully', new QuizResource($quiz), Response::HTTP_CREATED);
    }

    /*
    Update quiz questions and its options
     */
    public function updateQuestion(UpdateQuizQuestionRequest $request, Quiz $quiz, QuizQuestion $question)
    {
        if (Gate::forUser(Auth::user())->denies('update-quiz-question', $question)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($question->quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('You cannot update question of a published quiz', Response::HTTP_FORBIDDEN);
        }

        $question = $this->quizService->updateQuestion($request, $quiz, $question);

        return ResponseUtils::sendResponseWithSuccess('Quiz question updated successfully', new QuizQuestionResource($question), Response::HTTP_OK);
    }

    /*
    Delete a quiz question
     */
    public function deleteQuestion(Quiz $quiz, QuizQuestion $question)
    {
        if (Gate::forUser(Auth::user())->denies('delete-quiz-question', $question)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('You cannot delete question option of a published quiz', Response::HTTP_FORBIDDEN);
        }

        $success = $this->quizService->deleteQuestion($quiz, $question);

        if ($success) {
            return ResponseUtils::sendResponseWithoutData('Quiz Question deleted successfully', Response::HTTP_OK);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to delete quiz quesion', Response::HTTP_UNAUTHORIZED);
        }
    }

    /*
    Delete question options
     */
    public function deleteQuestionOption(Quiz $quiz, QuizQuestion $question, QuizQuestionOption $questionOption)
    {
        if (Gate::forUser(Auth::user())->denies('delete-quiz-question-option', $questionOption)) {
            return ResponseUtils::sendResponseWithError('You are not allowed to perform this action', Response::HTTP_UNAUTHORIZED);
        }

        if ($quiz->isPublished()) {
            return ResponseUtils::sendResponseWithError('You cannot delete question option of a published quiz', Response::HTTP_FORBIDDEN);
        }

        $success = $this->quizService->deleteQuestionOption($quiz, $question, $questionOption);

        if ($success) {
            return ResponseUtils::sendResponseWithoutData('Question option deleted successfully', Response::HTTP_OK);
        } else {
            return ResponseUtils::sendResponseWithError('Something went wrong, Unable to delete quesion option', Response::HTTP_UNAUTHORIZED);
        }
    }
}
