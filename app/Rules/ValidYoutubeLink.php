<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Bootstrap\HandleExceptions;

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
        /*$match = preg_match("/^https\:\/\/(www)\.(youtube)\.(com)\/watch\?v\=.+|^https\:\/\/(youtu)\.be\/.+/i", $value);*/

        $match = @file_get_contents("https://www.youtube.com/oembed?url=".$value."&format=json");

        if($match == false)
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
