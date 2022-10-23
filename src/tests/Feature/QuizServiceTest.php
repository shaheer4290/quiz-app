<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testsQuizCreatedSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $payload = [
            'title' => 'Test Quiz',
        ];

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/quizzes', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'title',
                    'slug',
                    'status',
                    'created_by',
                    'questions',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Quiz created successfully',
            ]);
    }

    public function testsRequiresQuizTitle()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $payload = [];
        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/quizzes', $payload);

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field is required.'],
                ],
            ]);
    }

    public function testsQuizUpdatedSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $payload = [
            'title' => 'Updated Test Quiz',
        ];
        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->putJson('/api/quizzes/'.$quiz->slug, $payload);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'title',
                    'slug',
                    'status',
                    'created_by',
                    'questions',
                ],
            ])
            ->assertJsonFragment(['title' => 'Updated Test Quiz']);
    }

    public function testsCannotUpdatePublishedQuiz()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'status' => 'published',
            'created_by' => $user->id,
        ]);

        $payload = [
            'title' => 'Updated Test Quiz',
        ];

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->putJson('/api/quizzes/'.$quiz->slug, $payload);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Published quiz cannot be updated',
                'error' => [
                    'message' => 'Published quiz cannot be updated',
                ],
            ]);
    }

    public function testsCannotPublishedQuizWithoutQuesions()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/quizzes/'.$quiz->slug.'/publish');

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Quiz must have atleast 1 question to be published',
                'error' => [
                    'message' => 'Quiz must have atleast 1 question to be published',
                ],
            ]);
    }

    public function testsQuizPublishedSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $quiz = Quiz::factory()
                        ->create([
                            'title' => 'Test Quiz',
                            'status' => 'draft',
                            'created_by' => $user->id,
                        ]);

        $quizQuestion = QuizQuestion::factory()
                                    ->create([
                                        'quiz_id' => $quiz->id,
                                    ]);

        $quizQuestionOption = QuizQuestionOption::factory()
                                    ->create([
                                        'question_id' => $quizQuestion->id,
                                        'is_correct' => true,
                                    ]);

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/quizzes/'.$quiz->slug.'/publish');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Quiz successfully published',
            ]);
    }
}
