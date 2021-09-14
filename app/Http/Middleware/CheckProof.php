<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckProof
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
        $rules = array(
              'bukti'=>['required','mimes:jpeg,jpg,png,gif','max:1024'],
              'note'=>['max:250'],
            );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = $validator->errors();
            $data_error = [
              'err'=>'validation',
              'bukti'=>$error->first('bukti'),
              'note'=>$error->first('note'),
            ];

            return response()->json($data_error);
        }
        return $next($request);
    }
}
