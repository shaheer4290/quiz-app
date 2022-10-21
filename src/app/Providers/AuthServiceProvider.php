<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use App\Policies\QuizPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // Quiz::class => QuizPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('delete-quiz', function (User $user, Quiz $quiz) {
            return $user->id === $quiz->created_by;
        });

        Gate::define('update-quiz', function (User $user, Quiz $quiz) {
            return $user->id === $quiz->created_by;
        });

        Gate::define('publish-quiz', function (User $user, Quiz $quiz) {
            return $user->id === $quiz->created_by;
        });

        Gate::define('add-quiz-questions', function (User $user, Quiz $quiz) {
            return $user->id === $quiz->created_by;
        });

        Gate::define('update-quiz-question', function (User $user, QuizQuestion $question) {
            return $user->id === $question->quiz->created_by;
        });

        Gate::define('delete-quiz-question', function (User $user, QuizQuestion $question) {
            return $user->id === $question->quiz->created_by;
        });

        Gate::define('delete-quiz-question-option', function (User $user, QuizQuestionOption $option) {
            return $user->id === $option->question->quiz->created_by;
        });
    }
}
