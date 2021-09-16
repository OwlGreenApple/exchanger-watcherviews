<?php

namespace App\Http\Middleware;

use Closure, Validator;
use Illuminate\Http\Request;
use App\Helpers\Price;
use App\Helpers\Api;
use App\Rules\MinCoin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class CheckMaxCoin
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
        $error = false;
        $method = $request->wallet_option;
        $auth = Auth::user();
        $wallet = $auth->coin;
        $wt_id = Auth::user()->watcherviews_id;

        $pc = new Price;
        $coin = $pc::convert_number($request->amount);

        $fee = ($coin * 25)/1000;
        $net_coin = $coin + $fee;

        if($method == 1)
        {
            $rules = [
            'amount'=>['required',new MinCoin($coin,false)],
            ];
        }
        else
        {
            $rules = [
                'amount'=>['required'],
            ];

            
            $wallet = $wallet - $net_coin;
        }

        // dd($wallet);
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = true;
            $err = $validator->errors();
            $errors = [
                'err'=>'validation',
                'coin_ammount'=>str_replace(["100000","ammount"],['100.000','jumlah coin'],$err->first('amount')),
            ];
        } 

        // VALIDATOR TO PREVENT INSUFFICENT COIN ON WALLET
        if($wallet < 0 && $method !== 1)
        {
            $error = true;
            $errors['err'] = 'coin';
            $errors['coin'] = Lang::get('custom.inscoin');
        }      

        // VALIDATOR TO PREVENT INSUFFICENT COIN ON WATCHERVIEWS
        if($wt_id > 0 && $method == 1)
        {
            $api = new Api;
            $wt_coin = $api->get_total_coin($wt_id);

            // if err = 1 invalid user id
            // if err = 2 invalid token
            if($wt_coin['err'] == 0)
            {
                $wtc = $wt_coin['total_coin'] - $net_coin;
                if($wtc < 0)
                {
                    $error = true;
                    $errors['err'] = 'coin';
                    $errors['coin'] = Lang::get('custom.inscoin');
                }
            }
            else
            {
                $error = true;
                $errors['err'] = 'api';
                $errors['coin'] = Lang::get('transaction.api');
            }
        }
 

        if($error == true)
        {
            return response()->json($errors);
        }

        return $next($request);
    }
}
