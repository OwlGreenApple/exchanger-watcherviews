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
        $rules = [
            'wt_email'=>['required','email'],
            'wt_pass'=>['required','max:255'],
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = true;
            $err = $validator->errors();
            $errors = [
                'err'=>'validation',
                'wt_email'=>$err->first('wt_email'),
                'wt_pass'=>$err->first('wt_pass'),
            ];
        }

        $price = new Price;
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
        }

        if($error == true)
        {
            return response()->json($errors);
        }

        return $next($request);
    }
}
