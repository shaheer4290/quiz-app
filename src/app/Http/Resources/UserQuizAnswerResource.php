<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserQuizAnswerResource extends JsonResource
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'question' => isset($this->question) ? $this->question->question : 'Question N/A',
            'score' => round($this->score, 3),
            'selected_options' => $this->answers,
            'options' => isset($this->question->options) ? QuizQuestionOptionResource::collection($this->question->options) : 'Options Not Available',
        ];
    }
}
