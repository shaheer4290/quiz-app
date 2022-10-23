<?php

namespace App\Services;

use App\Http\Resources\QuizResource;
use App\Jobs\SendQuizCreationgEmailJob;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Repositories\QuizQuestionOptionRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuizRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class QuizServiceImpl implements QuizService
{
    private QuizRepository $quizRepository;

    private QuizQuestionRepository $quizQuestionRepository;

    private QuizQuestionOptionRepository $quizQuestionOptionRepository;

    private UserRepository $userRepository;

    public function __construct(QuizRepository $quizRepository, QuizQuestionRepository $quizQuestionRepository, QuizQuestionOptionRepository $quizQuestionOptionRepository, UserRepository $userRepository)
    {
        $this->quizRepository = $quizRepository;
        $this->quizQuestionRepository = $quizQuestionRepository;
        $this->quizQuestionOptionRepository = $quizQuestionOptionRepository;
        $this->userRepository = $userRepository;
    }

    public function get($quiz)
    {
        $getQuizFromRedis = $this->quizRepository->getQuizFromRedis($quiz->id);

        if (! empty($getQuizFromRedis)) {
            $quizData = json_decode($getQuizFromRedis, true);
            $createdBy = $quizData['created_by'];
            $quiz = $quizData['quiz'];

            if ($createdBy != Auth::user()->id) {
                self::removeCorrectOptions($quiz);
            }
        } else {
            if ($quiz->isPublished()) {
                $redisData = [
                    'created_by' => Auth::user()->id,
                    'quiz' => new QuizResource($quiz),
                ];
                $this->quizRepository->saveToRedis($quiz->id, $redisData);
            }

            $quiz = new QuizResource($quiz);
        }

        return $quiz;
    }

    public function getAll()
    {
        $quizzes = $this->quizRepository->getAll();

        return $quizzes;
    }

    public function add($request)
    {
        $quiz = $this->quizRepository->create($request);

        return $quiz;
    }

    public function update($request, $quiz)
    {
        $quiz = $this->quizRepository->update($request, $quiz);

        return $quiz;
    }

    public function delete($quiz)
    {
        $success = $this->quizRepository->delete($quiz);

        return $success;
    }

    public function publish($quiz)
    {
        if (count($quiz->questions) == 0) {
            abort(Response::HTTP_BAD_REQUEST, 'Quiz must have atleast 1 question to be published');
        }

        $questioWithNoOptions = 0;
        $questioWithNoOptionsArr = [];

        foreach ($quiz->questions as $question) {
            if (count($question->options) == 0) {
                $questioWithNoOptions++;
                array_push($questioWithNoOptionsArr, $question->id);
            }
        }

        if ($questioWithNoOptions > 0) {
            abort(Response::HTTP_BAD_REQUEST, 'These question(s) with ID(s)'.implode(',', $questioWithNoOptionsArr).' have no options. Please add options for them before publishing');
        }

        $success = $this->quizRepository->publish($quiz);

        if ($success) {
            $usersForEmail = $this->userRepository->getUsersForEmail(Auth::user()->id);
            if (! empty($usersForEmail)) {
                dispatch(new SendQuizCreationgEmailJob($usersForEmail, $quiz));
            }
        }

        $redisData = [
            'created_by' => Auth::user()->id,
            'quiz' => new QuizResource($quiz),
        ];
        $this->quizRepository->saveToRedis($quiz->id, $redisData);

        return $success;
    }

    public function getQuestion($quiz, $question)
    {
        if (! $quiz->questions->contains($question)) {
            abort(Response::HTTP_NOT_FOUND, 'This quiz does not have the question with ID. '.$question->id);
        }

        return $question;
    }

    public function addQuestions($request, $quiz)
    {
        $request = $request->all();
        $existingQuestionsCount = count($quiz->questions);
        $questions = $request['questions'];

        if ($existingQuestionsCount + count($questions) > Quiz::MAXIMUM_QUESTIONS_COUNT) {
            abort(Response::HTTP_BAD_REQUEST, 'A quiz cannot have more than '.Quiz::MAXIMUM_QUESTIONS_COUNT.' questions. Already added question count is '.$existingQuestionsCount);
        }

        foreach ($questions as $question) {
            $options = $question['options'];

            $questionData = [];
            $questionData['quiz_id'] = $quiz->id;
            $questionData['question'] = $question['question'];
            $questionData['correct_answer'] = $question['correct_answer'];

            $newQuizQuestion = $this->quizQuestionRepository->create($questionData);

            foreach ($options as $option) {
                $optionData = [];
                $optionData['question_id'] = $newQuizQuestion->id;
                $optionData['option'] = $option['option'];
                $optionData['is_correct'] = $option['is_correct'];

                $newQuizQuestionOption = $this->quizQuestionOptionRepository->create($optionData);
            }
        }

        return $quiz->fresh();
    }

    public function updateQuestion($request, $quiz, $question)
    {
        if (! $quiz->questions->contains($question)) {
            abort(Response::HTTP_BAD_REQUEST, 'This quiz does not have the question with ID. '.$question->id);
        }

        $request = $request->all();
        $existingQuestionOptions = [];

        $existingQuestionOptionsCount = count($question->options);

        if ($existingQuestionOptionsCount > 0) {
            foreach ($question->options as $questionOption) {
                $existingQuestionOptions[$questionOption->id] = $questionOption;
            }
        }
        // updating options //
        if (isset($request['options']) && ! empty($request['options'])) {
            $existingOptionsInReq = array_filter($request['options'], function ($var) {
                return isset($var['id']);
            });

            $newOptionsCount = count($request['options']) - count($existingOptionsInReq);

            if ($existingQuestionOptionsCount + $newOptionsCount > QuizQuestion::MAXIMUM_OPTIONS_COUNT) {
                abort(Response::HTTP_BAD_REQUEST, 'A question cannot have more than '.QuizQuestion::MAXIMUM_OPTIONS_COUNT.' options. Already added option count is '.$existingQuestionOptionsCount);
            }

            foreach ($request['options'] as $option) {
                $optionData = [];
                $optionData['option'] = $option['option'];
                $optionData['is_correct'] = $option['is_correct'];

                if (isset($option['id']) && ! empty($option['id'])) {
                    // checking if wrong question id is passed
                    if (! array_key_exists($option['id'], $existingQuestionOptions)) {
                        abort(Response::HTTP_BAD_REQUEST, 'The option id '.$option['id'].' does belong to this question.');
                    }
                    $this->quizQuestionOptionRepository->update($option['id'], $optionData);
                } else {
                    $optionData['question_id'] = $question->id;
                    $this->quizQuestionOptionRepository->create($optionData);
                }
            }
        }
        // updating quesion
        $updatedQuestionData = [];
        if (isset($request['question']) && ! empty($request['question'])) {
            $updatedQuestionData['question'] = $request['question'];
        }

        if (isset($request['correct_answer']) && ! empty($request['correct_answer'])) {
            $updatedQuestionData['correct_answer'] = $request['correct_answer'];
        }

        if (! empty($updatedQuestionData)) {
            $this->quizQuestionRepository->update($question->id, $updatedQuestionData);
        }

        return $question->fresh();
    }

    public function deleteQuestion($quiz, $question)
    {
        if (! $quiz->questions->contains($question)) {
            abort(Response::HTTP_BAD_REQUEST, 'This quiz does not have the question with ID. '.$question->id);
        }

        $success = $this->quizQuestionRepository->delete($question);

        return $success;
    }

    public function deleteQuestionOption($quiz, $question, $questionOption)
    {
        if (! $quiz->questions->contains($question)) {
            abort(Response::HTTP_BAD_REQUEST, 'This quiz does not have the question with ID. '.$question->id);
        }

        if (! $question->options->contains($questionOption)) {
            abort(Response::HTTP_BAD_REQUEST, 'This quiz question does not have the option with ID. '.$questionOption->id);
        }

        $success = $this->quizQuestionOptionRepository->delete($questionOption);

        return $success;
    }

    private function removeCorrectOptions(&$data)
    {
        foreach ($data['questions'] as $questionKey => $question) {
            foreach ($question['options'] as $optionKey => $option) {
                if (isset($data['questions'][$questionKey]['options'][$optionKey]['is_correct'])) {
                    unset($data['questions'][$questionKey]['options'][$optionKey]['is_correct']);
                }
            }
        }
    }
}
