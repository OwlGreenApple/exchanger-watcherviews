<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Rules\CheckUsersCredit;
use App\Rules\ValidYoutubeLink;
use App\Rules\CheckIdExchange;
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
        $reqs = [
          'exchange'=>$request->exchange,
          'link_video'=>$request->link_video,
          'views'=>(int)str_replace(".","",$request->views),
          'runs'=>(int)str_replace(".","",$request->runs),
        ];

        $maxvalue = 10000;
        $rules = [
          'link_video'=>['bail','required',new ValidYoutubeLink],
          'views'=>['bail','required',new CheckIdExchange($reqs['exchange']),'numeric','min:100','max:'.$maxvalue.'',new CheckUsersCredit($reqs['exchange'],0)]
        ];

        if($request->drip == "1")
        {
           $rules['views'] = ['bail','required','numeric','min:100','max:'.$maxvalue.''];
           $rules['runs'] = ['bail','required',new CheckIdExchange($reqs['exchange']),'numeric','min:1','max:'.$maxvalue.'',new CheckUsersCredit($reqs['exchange'],$reqs['views'])];
        }

        $validator = Validator::make($reqs,$rules);

        if($validator->fails() == true)
        {
           $err = $validator->errors();
           $errors = [
              'msg'=>2,
              'link_video'=>$err->first('link_video'),
              'views'=>$err->first('views'),
              'runs'=>$err->first('runs')
           ];

           return response()->json($errors);
        }

        $req = new Request($reqs);
        return $next($req);
    }
}