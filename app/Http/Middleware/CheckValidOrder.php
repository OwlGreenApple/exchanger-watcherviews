<?php

namespace App\Http\Middleware;

use App\Rules\CheckPricing;
use Closure, Validator, session;

class CheckValidOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // checkout from summary
        if($request->summary == true)
        {
            $req = ['package'=>session('order')['package']];
        }
        else
        {
            $req = $request->all();
        }

        //dd($req);
        
        $rules = [
            'package'=>[new CheckPricing]
        ];

        $validator = Validator::make($req,$rules);
        
        if($validator->fails() == true)
        {
            $error = $validator->errors();
            $errors = array(
                'status'=>0,
                'package'=>$error->first('package'),
            );

            return response()->json($errors);
        }

        return $next($request);
    }
}
