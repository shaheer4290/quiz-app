<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => empty($this->status) ? Quiz::STATUS_DRAFT : $this->status,
            'created_by' => $this->user->name,
            'questions' => QuizQuestionResource::collection($this->questions),
        ];
    }
}
