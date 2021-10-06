<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class CheckPaymentMethods implements Rule
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
        $auth = Auth::user();
        $illegal = false;

        if($auth->bank_1 == null && $auth->bank_2 == null && $auth->epayment_1 == null && $auth->epayment_2 == null && $auth->epayment_3 == null)
        {
            $illegal = true;
        }

        if($value == null && $illegal == true)
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
        return Lang::get('custom.payment');
    }
}
