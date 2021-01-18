<?php

namespace App\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Validation\Rule;

class CheckUsersCredit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $idpackage, $views;

    public function __construct($idpackage,$views)
    {
        $this->idpackage = $idpackage;
        $this->views = intval($views);
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
        $user = User::find(Auth::id());
        $current_credits = $user->credits;
        $coins_rate = intval(getExchangeRate($this->idpackage)['coins'])/1000;
        $views = $this->views;

        /*
           in case $views > 0 then 
           $value = $request->runs else
           $value = $request->views
        */

        if($views > 0)
        {
          $value = $value * $views * $coins_rate;
          $credits = $current_credits - $value;
        }
        else
        {
          $value = $value * $coins_rate;
          $credits = $current_credits - $value;
        }
       
        if($credits < 0)
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
        return 'Insufficient coins, please buy more.';
    }
}
