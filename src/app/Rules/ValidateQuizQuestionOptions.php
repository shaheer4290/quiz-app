<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateQuizQuestionOptions implements Rule
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
        $validationPasses = false;

        foreach ($value as $k => $v) {
            if ($v['is_correct'] == true) {
                $validationPasses = true;
                break;
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
        return 'Atleast one option needs to be true.';
    }
}
