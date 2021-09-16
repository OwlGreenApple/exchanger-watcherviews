<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Price;

class MinCoin implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $msg;
    public function __construct($coin,$sell)
    {
        $this->coin = $coin;
        $this->sell = $sell;
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
        $api = new Price;
        $membership = Auth::user()->membership;
        $arr = $api->check_type($membership);
       
        if($arr == false)
        {
            $this->msg = Lang::get('custom.failed');
            return $arr;
        }

        $coin_fee = $arr['fee'];
        $coin_fee = ($this->coin * $coin_fee)/100;
        $fee = $this->coin + $coin_fee;

        $coin_user = Auth::user()->coin;
        $calculate = $coin_user - $fee;

        if($calculate < 0 && $this->sell == true)
        {
            $this->msg = 'Saldo coin anda tidak cukup';
            return false;
        }

        $min = 100000;
        if($this->coin < $min)
        {
            if($this->sell == true)
            {
                $this->msg = 'Jumlah minimal coin untuk di jual adalah 100.000';
            }
            else
            {
                $this->msg = 'Jumlah minimal coin untuk withdraw adalah 100.000';
            }
            
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
        return $this->msg;
    }

/*end class*/
}
