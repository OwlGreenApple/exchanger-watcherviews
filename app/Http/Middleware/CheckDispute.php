<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Validator;

class CheckDispute
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
            'proof'=>['required','mimes:jpeg,jpg,png','max:1024'],
            'comments'=>['max:900'],
        ];

        if($request->role == 1)
        {
           $rules['identity'] = ['required','mimes:jpeg,jpg,png','max:1024'];
           $rules['mutation'] = ['required','mimes:jpeg,jpg,png','max:1024'];
        }

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $err = $validator->errors();
            $errors = [
                'err'=>'validation',
                'proof'=>str_replace("proof",'bukti pembayaran',$err->first('proof')),
                'identity'=>str_replace("identity",'profil',$err->first('identity')),
                'mutation'=>str_replace("mutation",'bukti mutasi',$err->first('mutation')),
                'comments'=>str_replace("comments",'komentar',$err->first('comments')),
            ];

            return response()->json($errors);
        } 

        return $next($request);
    }
}
