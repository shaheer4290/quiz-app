<?php

namespace App\Http\Requests;

use App\Models\QuizQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * The data to be validated should be processed as JSON.
     *
     * @return mixed
     */
    public function validationData()
    {
        return $this->json()->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'question' => 'sometimes|required|string',
            'correct_answer' => [
                'sometimes|required',
                Rule::in([QuizQuestion::SINGLE_CORRECT_ANSWER, QuizQuestion::MULTIPLE_CORRECT_ANSWER]),
            ],
            'options' => ['sometimes', 'required', 'array', 'min:1', 'max:5'],
            'options.*.id' => ['sometimes', 'required', 'integer', 'distinct'],
            'options.*.option' => 'required|string',
            'options.*.is_correct' => ['required', 'bool'],
        ];
    }
}
