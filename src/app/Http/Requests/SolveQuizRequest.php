<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolveQuizRequest extends FormRequest
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
            'solution' => ['required', 'array', 'min:1', 'max:10'],
            'solution.*.question_id' => ['required', 'integer', 'distinct'],
            'solution.*.selected_option_ids' => ['required', 'array', 'min:1', 'max:5'],
            'solution.*.selected_option_ids.*' => ['distinct'],
        ];
    }
}
