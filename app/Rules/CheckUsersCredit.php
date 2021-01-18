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
        $this->views = $views;
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
        $coins_rate = (int)getExchangeRate($this->idpackage)['coins'];
        $coins_rate = $coins_rate/1000;
        $view_request = $this->views;

        /*
           in case $views > 0 then 
           $value = $request->runs else
           $value = $request->views
        */

        if($view_request > 0)
        {
          $value = $value * $view_request * $coins_rate;
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
