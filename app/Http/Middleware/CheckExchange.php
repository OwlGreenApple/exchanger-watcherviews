<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Rules\CheckUsersCredit;
use Validator;

class CheckExchange
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
          'exchange'=>['required','numeric','min:1','max:7'],
          'total_views'=>['required','numeric','min:1','max:999',new CheckUsersCredit($request->exchange)]
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails() == true)
        {
           $err = $validator->errors();
           $errors = [
              'msg'=>2,
              'total_views'=>$err->first('total_views'),
              'exchange'=>$err->first('exchange')
           ];

           return response()->json($errors);
        }
       

        return $next($request);
    }
}
