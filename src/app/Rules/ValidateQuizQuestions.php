<?php

namespace App\Rules;

use App\Models\QuizQuestion;
use Illuminate\Contracts\Validation\Rule;

class ValidateQuizQuestions implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validationPasses = true;

        foreach ($value as $k => $v) {
            $correctAnwer = $v['correct_answer'];
            $correctAnwersCount = 0;

            if (isset($v['options']) && count($v['options']) > 0) {
                $options = $v['options'];

                foreach ($options as $option) {
                    if (isset($option['is_correct']) && $option['is_correct'] == true) {
                        $correctAnwersCount++;
                    }
                }
            }

            if ($correctAnwer == QuizQuestion::SINGLE_CORRECT_ANSWER && $correctAnwersCount != 1) {
                $validationPasses = false;
            }

            if ($correctAnwer == QuizQuestion::MULTIPLE_CORRECT_ANSWER && $correctAnwersCount == 0) {
                $validationPasses = false;
            }
        }

        return $validationPasses;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'For Single Correct Answer, Atmost One Option can be true & for Multiple Correct Answer Atleast one option should be true';
    }
}
