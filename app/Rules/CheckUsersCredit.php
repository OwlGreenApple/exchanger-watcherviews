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

    public $idpackage;

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
        $user = User::find(Auth::id());
        $current_credits = $user->credits;
        $coins_rate = getExchangeRate($this->idpackage)['coins'];
        $value = $value * $coins_rate;
        $credits = $current_credits - $value;

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
