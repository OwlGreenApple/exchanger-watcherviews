<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use App\Models\User;

class CheckUniquePhone implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $code;
    public $phone;

    public function __construct($code,$phone)
    {
        $this->code = $code;
        $this->phone = $phone;
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
        $phone = $this->code.$this->phone;
        $user = User::where('phone_number',$phone)->first();

        if(is_null($user))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('custom.unique_phone');
    }
}
