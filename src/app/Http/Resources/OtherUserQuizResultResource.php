<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OtherUserQuizResultResource extends JsonResource
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
            'quiz_title' => $this->quiz->title,
            'slug' => $this->quiz->slug,
            'attempted_by' => $this->user->name,
            'email' => $this->user->email,
            'total_score' => round($this->score, 3),
            'total_correctness_percentage' => round(($this->score / count($this->solutions) * 100), 3),
            'answers' => OtherUserQuizAnswerResource::collection($this->solutions),
        ];
    }
}
