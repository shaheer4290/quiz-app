<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_quiz_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_quiz_id')
                  ->constrained('user_quizzes')
                  ->onDelete('cascade');
            $table->integer('question_id');
            $table->json('answers');
            $table->double('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_quiz_solutions');
    }
};
