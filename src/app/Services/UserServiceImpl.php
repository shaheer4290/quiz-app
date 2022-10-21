<?php

namespace App\Services;

use App\Models\QuizQuestion;
use App\Repositories\QuizRepository;
use App\Repositories\UserQuizRepository;
use App\Repositories\UserQuizSolutionRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserServiceImpl implements UserService
{
    private UserQuizRepository $userQuizRepository;

    private UserQuizSolutionRepository $userQuizSolutionRepository;

    private QuizRepository $quizRepository;

    public function __construct(UserQuizRepository $userQuizRepository, UserQuizSolutionRepository $userQuizSolutionRepository, QuizRepository $quizRepository)
    {
        $this->userQuizRepository = $userQuizRepository;
        $this->userQuizSolutionRepository = $userQuizSolutionRepository;
        $this->quizRepository = $quizRepository;
    }

    public function solveQuiz($request, $quiz)
    {
        $request = $request->all();

        $solutionMap = [];
        $userSolutionMap = [];
        $totalScore = 0;
        // creating questions map with their solutions
        foreach ($quiz->questions as $question) {
            $correctOptions = [];
            $wrongOptions = [];

            foreach ($question->options as $option) {
                if ($option->is_correct) {
                    array_push($correctOptions, $option->id);
                } else {
                    array_push($wrongOptions, $option->id);
                }
            }

            $solutionMap[$question->id] = [
                'correctAnswer' => $question->correct_answer,
                'correctOptions' => $correctOptions,
                'wrongOptions' => $wrongOptions,
            ];

            $userSolutionMap[$question->id] = [
                'score' => 0,
                'answers' => [],
            ];
        }

        foreach ($request['solution'] as $solution) {
            $questionId = $solution['question_id'];
            $selectedOptions = $solution['selected_option_ids'];

            // validation for question id passed
            if (! array_key_exists($questionId, $solutionMap)) {
                abort(Response::HTTP_FORBIDDEN, 'Quiestion ID '.$questionId.' does not belong to this quiz. Please add correct question id');
            }

            $currentQuestion = $solutionMap[$questionId];

            // validation for no of correct answers for a question
            if ($currentQuestion['correctAnswer'] == QuizQuestion::SINGLE_CORRECT_ANSWER && count($selectedOptions) > 1) {
                abort(Response::HTTP_FORBIDDEN, 'Quiestion ID '.$questionId.' has only one correct answer. You cannot select multiple options');
            }

            // validation for no of correct option id passed for a question
            $selectedOptionsCollection = collect($selectedOptions);
            $diff = $selectedOptionsCollection->diff(array_merge($currentQuestion['correctOptions'], $currentQuestion['wrongOptions']));
            $diff = $diff->all();
            if (count($diff) > 0) {
                abort(Response::HTTP_FORBIDDEN, 'Option id(s) '.implode(',', $diff).' does not exist for question id '.$questionId);
            }

            $userSolutionMap[$questionId]['answers'] = $selectedOptions;
            // calculating score
            if ($currentQuestion['correctAnswer'] == QuizQuestion::SINGLE_CORRECT_ANSWER) {
                if ($selectedOptions == $currentQuestion['correctOptions']) {
                    $userSolutionMap[$questionId]['score'] = 1;
                } else {
                    $userSolutionMap[$questionId]['score'] = -1;
                }
            } else {
                $rightOptionsSelected = 0;
                $wrongOptionsSelected = 0;
                $correctnessWeightForRight = 0;
                $correctnessWeightForWrong = 0;

                foreach ($selectedOptions as $option) {
                    if (in_array($option, $currentQuestion['correctOptions'])) {
                        $rightOptionsSelected++;
                    } else {
                        $wrongOptionsSelected++;
                    }
                }

                if ($rightOptionsSelected > 0) {
                    $correctnessWeightForRight = $rightOptionsSelected / count($currentQuestion['correctOptions']);
                }

                if ($wrongOptionsSelected > 0) {
                    $correctnessWeightForWrong = $wrongOptionsSelected / count($currentQuestion['wrongOptions']);
                }

                $userSolutionMap[$questionId]['score'] = $correctnessWeightForRight - $correctnessWeightForWrong;
            }

            $totalScore += $userSolutionMap[$questionId]['score'];
        }

        $userQuiz = $this->userQuizRepository->addUserQuiz([
            'user_id' => Auth::user()->id,
            'quiz_id' => $quiz->id,
            'score' => $totalScore,
        ]);

        foreach ($userSolutionMap as $key => $userSolution) {
            $userQuizSolData = [
                'user_quiz_id' => $userQuiz->id,
                'question_id' => $key,
                'answers' => $userSolution['answers'],
                'score' => $userSolution['score'],
            ];

            $this->userQuizSolutionRepository->addUserSolution($userQuizSolData);
        }
    }

    public function getQuizResult($quiz)
    {
        $userQuiz = $this->userQuizRepository->getUserQuiz(Auth::user()->id, $quiz->id);

        return $userQuiz;
    }

    public function getMyQuizzes()
    {
        $myQuizzes = $this->quizRepository->getmyQuizzes(Auth::user()->id);

        return $myQuizzes;
    }

    public function getAllQuizResult()
    {
        $userQuizzes = $this->userQuizRepository->getAllUserQuizzes(Auth::user()->id);

        return $userQuizzes;
    }

    public function getOtherUsersQuizResult()
    {
        $otherUserQuizzes = $this->userQuizRepository->getAllOtherUserQuizzes(Auth::user()->id);

        return $otherUserQuizzes;
    }
}
