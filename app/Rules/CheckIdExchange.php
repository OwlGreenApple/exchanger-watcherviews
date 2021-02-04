<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckIdExchange implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($idpackage)
    {
        $this->idpackage = $idpackage;
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
        //ignore $value
        if($this->idpackage == null || $this->idpackage == "" || empty($this->idpackage))
        {
          return false;
        }
        elseif(is_numeric($this->idpackage) == false)
        {
          return false;
        } 
        elseif((int)$this->idpackage < 1 || (int)$this->idpackage > 7 )
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
        return 'Invalid package.';
    }
}
