<?php

namespace App\Services;

interface QuizService
{
    public function get($quiz);

    public function getAll();

    public function add($request);

    public function update($request, $quiz);

    public function delete($id);

    public function publish($quiz);

    public function getQuestion($quiz, $question);

    public function addQuestions($request, $quiz);

    public function updateQuestion($request, $quiz, $question);

    public function deleteQuestion($quiz, $question);

    public function deleteQuestionOption($quiz, $question, $questionOption);
}
