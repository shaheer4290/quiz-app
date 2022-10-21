<?php

namespace App\Repositories;

use App\Models\Quiz;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class QuizRepositoryImpl implements QuizRepository
{
    public function getAll()
    {
        return Quiz::where('status', Quiz::STATUS_PUBLISHED)->paginate(10);
    }

    public function getmyQuizzes($id)
    {
        return Quiz::where('created_by', $id)->paginate(10);
    }

    public function getBySlug($slug)
    {
        return Quiz::where('slug', $slug)->first();
    }

    public function getById($id)
    {
        return Quiz::where('id', $id)->first();
    }

    public function create($request)
    {
        $quiz = new Quiz();
        $quiz->title = $request->title;
        $quiz->slug = SlugService::createSlug(Quiz::class, 'slug', $request->title);
        $quiz->created_by = Auth::user()->id;

        if (! $quiz->save()) {
            return null;
        } else {
            return $quiz;
        }
    }

    public function update($request, $quiz)
    {
        $quiz->title = $request->title;
        $quiz->slug = SlugService::createSlug(Quiz::class, 'slug', $request->title);

        if (! $quiz->save()) {
            return null;
        } else {
            return $quiz;
        }
    }

    public function delete($quiz)
    {
        return $quiz->delete();
    }

    public function publish($quiz)
    {
        $quiz->status = Quiz::STATUS_PUBLISHED;

        if ($quiz->save()) {
            return true;
        }

        return false;
    }

    public function saveToRedis($id, $data)
    {
        $key = 'quiz-'.$id;
        Redis::set($key, json_encode($data));
    }

    public function getQuizFromRedis($id)
    {
        $key = 'quiz-'.$id;

        return Redis::get($key);
    }
}
