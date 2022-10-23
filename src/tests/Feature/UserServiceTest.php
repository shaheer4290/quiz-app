<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use App\Models\UserQuiz;
use App\Models\UserQuizSolution;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testsQuizSolutionSubmittedSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user2@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()
                        ->create([
                            'title' => 'Test Quiz',
                            'slug' => 'dfa',
                            'status' => 'published',
                            'created_by' => $user->id,
                        ]);

        $quizQuestion = QuizQuestion::factory()
                                    ->create([
                                        'quiz_id' => $quiz->id,
                                        'correct_answer' => 'single',
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);
        $quizQuestionOption2 = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => false,
                                    ]);

        $payload = [
            'solution' => [
                [
                    'question_id' => $quizQuestion->id,
                    'selected_option_ids' => [$quizQuestionOption->id],
                ],
            ],
        ];

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/users/quizzes/'.$quiz->slug.'/attempt', $payload);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Quiz submitted successfully',
            ]);
    }

    public function testsGetQuizResultSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user2@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()
                        ->create([
                            'title' => 'Test Quiz',
                            'slug' => 'dfa',
                            'status' => 'published',
                            'created_by' => $user->id,
                        ]);

        $quizQuestion = QuizQuestion::factory()
                                    ->create([
                                        'quiz_id' => $quiz->id,
                                        'correct_answer' => 'single',
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);

        $quizQuestionOption2 = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);
        $quizQuestionOption3 = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);
        $quizQuestionOption4 = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => false,
                                    ]);
        $quizQuestionOption5 = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => false,
                                    ]);

        $quizSol = UserQuiz::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 0.667,
        ]);

        $userQuizSol = UserQuizSolution::factory()->create([
            'user_quiz_id' => $quizSol->id,
            'question_id' => $quizQuestion->id,
            'answers' => json_encode([$quizQuestionOption->id, $quizQuestionOption2->id, $quizQuestionOption4->id]),
        ]);

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/quizzes/'.$quiz->slug);

        $response
            ->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'quiz_title',
                     'slug',
                     'created_by',
                     'total_score',
                     'total_correctness_percentage',
                     'answers',
                 ],
             ])->assertJsonFragment(['total_score' => 0.667, 'total_correctness_percentage' => 66.7]);
    }

    public function testsQuizNotTaken()
    {
        $user = User::factory()->create([
            'email' => 'test.user2@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()
                        ->create([
                            'title' => 'Test Quiz',
                            'slug' => 'dfa',
                            'status' => 'published',
                            'created_by' => $user->id,
                        ]);

        $quizQuestion = QuizQuestion::factory()
                                    ->create([
                                        'quiz_id' => $quiz->id,
                                        'correct_answer' => 'single',
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/quizzes/'.$quiz->slug);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You have not taken the quiz yet',
                'error' => [
                    'message' => 'You have not taken the quiz yet',
                ],
            ]);
    }

    public function testsAlreadyTakenQuiz()
    {
        $user = User::factory()->create([
            'email' => 'test.user2@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()
                        ->create([
                            'title' => 'Test Quiz',
                            'slug' => 'dfa',
                            'status' => 'published',
                            'created_by' => $user->id,
                        ]);

        $quizQuestion = QuizQuestion::factory()
                                    ->create([
                                        'quiz_id' => $quiz->id,
                                        'correct_answer' => 'single',
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);

        $quizSol = UserQuiz::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
        ]);

        $payload = [
            'solution' => [
                [
                    'question_id' => $quizQuestion->id,
                    'selected_option_ids' => [$quizQuestionOption->id],
                ],
            ],
        ];

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/users/quizzes/'.$quiz->slug.'/attempt', $payload);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You have already taken the quiz',
                'error' => [
                    'message' => 'You have already taken the quiz',
                ],
            ]);
    }
}
