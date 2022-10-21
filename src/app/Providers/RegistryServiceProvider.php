<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\AuthServiceImpl;
use App\Services\QuizService;
use App\Services\QuizServiceImpl;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class RegistryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthService::class, AuthServiceImpl::class);
        $this->app->bind(QuizService::class, QuizServiceImpl::class);
        $this->app->bind(UserService::class, UserServiceImpl::class);
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
