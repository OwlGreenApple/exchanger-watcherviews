<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\InternationalTel;
use App\Rules\CheckUserPhone;
use App\Rules\OldPassword;

class CheckUserProfile
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
        $userid = Auth::id();
        $phone = strip_tags($request->phone);

        $rules = [
            'username' => ['required','string','max:190'],
        ];

        if(!empty($phone))
        {
          $rules['phone'] = ['min:6','max:18',new InternationalTel,new CheckUserPhone($request->code_country,$userid)];
        }

        if(!empty($request->oldpass) && !empty($request->confpass) || !empty($request->password))
        {
            $rules['oldpass'] = ['required','string', new OldPassword];
            $rules['password'] = ['required','string', 'min:8', 'max:32'];
            $rules['confpass'] = [ 'required','string', 'min:8', 'max:32', 'same:password'];
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $err = $validator->errors();
            $error = array(
              'error'=>2,
              'username'=>$err->first('username'),
              'phone'=>$err->first('phone'),
              'oldpass'=>$err->first('oldpass'),
              'password'=>$err->first('password'),
              'confpass'=>$err->first('confpass'),
            );

            return response()->json($error);
        }

        return $next($request);
    }
    
/*end middleware*/
}
