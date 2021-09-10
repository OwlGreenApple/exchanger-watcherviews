<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Price;
use App\Models\Transaction;
use Carbon\Carbon;
use Validator;

class CheckSelling
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $coin = str_replace(".","",$request->tr_coin);
        $coin = (int)$coin;

        $rules = [
            'tr_coin'=>['bail','required','numeric',/*new MinCoin($coin),*/ new CheckMaxSell]
        ];

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails() == true)
        {
            $err = $validator->errors();
            $errors['err'] = 1;
            $errors['tr_coin'] = str_replace("tr_coin",Lang::get('transaction.total.coin'),$err->first('tr_coin'));

            return response()->json($errors);
        }
        return $next($request);
    }

/*end class*/
}

class CheckMaxSell implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $msg;
    public function __construct()
    {
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
        $membership = Auth::user()->membership;
        $api = new Price;
        $arr = $api->check_type($membership);
        
        if($arr == false)
        {
            $this->msg = Lang::get('custom.failed');
            return $arr;
        }
        
        $max_trans = $arr['max_trans']; // trans per day
        $max_sell = $arr['max_sell']; // trans per month

        /*
            PUT LOGIC HERE .....

            if($this>logic == 1)
        */

        return $this->check_trans_day($max_trans);
    }

    public function check_trans_day($trans)
    {
        // $today = Carbon::now()->toDateString();
        $tr = Transaction::selectRaw('SUM(total) AS total_sell_day')->whereRaw('DATE(created_at) = CURDATE()')->where('seller_id',Auth::id())->first();

        if($tr->total_sell_day > $trans)
        {
            $this->msg = 'Anda hanya dapat menjual coin maksimal Rp '.$trans.' per hari';
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