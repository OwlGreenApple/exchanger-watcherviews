<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidYoutubeLink implements Rule
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
        $match = preg_match("/^https\:\/\/(www)\.(youtube)\.(com)\/watch\?v\=.+/i", $value);
        if($match == 0)
        {
          return false;
        }
        else
        {
          return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid youtube link.';
    }
}
