<?php

namespace App\Providers;

use App\Repositories\QuizQuestionOptionRepository;
use App\Repositories\QuizQuestionOptionRepositoryImpl;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuizQuestionRepositoryImpl;
use App\Repositories\QuizRepository;
use App\Repositories\QuizRepositoryImpl;
use App\Repositories\UserQuizRepository;
use App\Repositories\UserQuizRepositoryImpl;
use App\Repositories\UserQuizSolutionRepository;
use App\Repositories\UserQuizSolutionRepositoryImpl;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
        $this->app->bind(QuizRepository::class, QuizRepositoryImpl::class);
        $this->app->bind(QuizQuestionRepository::class, QuizQuestionRepositoryImpl::class);
        $this->app->bind(QuizQuestionOptionRepository::class, QuizQuestionOptionRepositoryImpl::class);
        $this->app->bind(UserQuizRepository::class, UserQuizRepositoryImpl::class);
        $this->app->bind(UserQuizSolutionRepository::class, UserQuizSolutionRepositoryImpl::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
