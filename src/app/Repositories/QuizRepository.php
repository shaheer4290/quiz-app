<?php

namespace App\Repositories;

interface QuizRepository
{
    public function getAll();

    public function getmyQuizzes($id);

    public function getBySlug($slug);

    public function getById($id);

    public function create($request);

    public function update($request, $quiz);

    public function delete($quiz);

    public function publish($quiz);

    public function saveToRedis($id, $data);

    public function getQuizFromRedis($id);
}
