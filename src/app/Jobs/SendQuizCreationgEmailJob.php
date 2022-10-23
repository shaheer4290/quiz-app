<?php

namespace App\Jobs;

use App\Mail\QuizCreationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendQuizCreationgEmailJob implements ShouldQueue
{
    protected $users;

    protected $quiz;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, $quiz)
    {
        $this->users = $users;
        $this->quiz = $quiz;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user) {
            $email = new QuizCreationEmail($user, $this->quiz);
            Mail::to($user->email)->send($email);
        }
    }
}
