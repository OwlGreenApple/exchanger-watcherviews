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
        $api = new Price;
        $kurs = $api->get_rate();
        $total = $kurs * $coin;

        $rules = [
            'tr_coin'=>['bail','required','numeric',new MinCoin($coin),new CheckMaxSell('chance',$total),new CheckMaxSell('day',$total)]
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
    public function __construct($status,$total)
    {
        $this->status = $status;
        $this->total = $total;
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
        $chance_sell = $arr['sell']; // trans chance / day

        /*
            'day' = CEK MAX PENJUALAN / HARI 
            'month' = CEK MAX PENJUALAN / BULAN
            'chance' = CEK MAX PENJUALAN / BULAN
        */

        if($this->status == 'day')
        {
            return $this->check_trans_day($max_trans,$this->total);
        }

        if($this->status == 'chance')
        {
            return $this->check_trans_chance($chance_sell,$this->total);
        }

       /* if($this->status == 'month')
        {
            return $this->check_trans_month($max_sell);
        }*/
        
    }

    public function check_trans_day($trans,$total)
    {
        $err_msg = 'Anda hanya dapat menjual coin maksimal Rp '.$trans.' per hari';
        if($total > $trans)
        {
            $this->msg = $err_msg;
            return false;
        }

        $tr = Transaction::selectRaw('SUM(total) AS total_sell_day')->whereRaw('DATE(created_at) = CURDATE()')->where('seller_id',Auth::id())->first();

        if($tr->total_sell_day > $trans)
        {
            $this->msg = $err_msg;
            return false;
        }
        else
        {
            return true;
        }
    }

    public function check_trans_chance($sell)
    {
        $tr = Transaction::where('seller_id',Auth::id())->whereRaw('DATE(created_at) = CURDATE()')->get();

        $total_sell_day = $tr->count();

        if($total_sell_day >= $sell)
        {
            $this->msg = 'Kesempatan anda untuk menjual coin hanya '.$sell.' x per hari';
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

class MinCoin implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $msg;
    public function __construct($coin)
    {
        $this->coin = $coin;
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

        if($calculate < 0)
        {
            $this->msg = 'Saldo coin anda tidak cukup';
            return false;
        }

        $min = 100000;
        if($this->coin < $min)
        {
            $this->msg = 'Jumlah minimal coin untuk di jual adalah 100.000';
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