<?php

namespace App\Http\Middleware;

use Closure, Validator;
use Illuminate\Http\Request;
use App\Helpers\Price;
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

        if($method == 1)
        {
            $rules = [
            'coin_ammount'=>['required','numeric','min:100000'],
            ];
        }
        else
        {
            $rules = [
                'coin_ammount'=>['required','numeric'],
            ];

            $wallet = $wallet - $request->coin_ammount;
        }
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = true;
            $err = $validator->errors();
            $errors = [
                'err'=>'validation',
                'coin_ammount'=>str_replace(["100000","coin ammount"],['100.000','jumlah coin'],$err->first('coin_ammount')),
            ];
        } 

        if($wallet < 0)
        {
            $error = true;
            $errors['err'] = 'coin';
            $errors['coin'] = Lang::get('custom.inscoin');
        }       

        /*$price = new Price;
        $auth = Auth::user();
        $wallet = $auth->coin;
        $package = $price->check_type($auth->membership);

        //INVALID PACKAGE USUALLY THE RISK OF CHANGE PACKAGE NAME
        if($package == false)
        {
            $error = true;
            $errors['pkg'] = Lang::get('custom.failed');
        }

        // IF USER'S COIN IN WALLET REACH MAXIUM ACCORDING ON MEMBERSHIP
        if($wallet >= $package['max_coin'])
        {
            $error = true;
            $errors['max'] = Lang::get('custom.max');
        }*/

        if($error == true)
        {
            return response()->json($errors);
        }

        return $next($request);
    }
}
