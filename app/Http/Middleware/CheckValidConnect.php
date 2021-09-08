<?php

namespace App\Http\Middleware;

use Closure, Validator;
use Illuminate\Http\Request;
use App\Helpers\Price;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class CheckValidConnect
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
        $rules = [
            'wt_email'=>['required','email'],
            'wt_pass'=>['required','max:255'],
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $err = $validator->errors();
            $errors = [
                'err'=>'validation',
                'wt_email'=>$err->first('wt_email'),
                'wt_pass'=>$err->first('wt_pass'),
            ];
            return response()->json($errors);
        }

        return $next($request);
    }
}
