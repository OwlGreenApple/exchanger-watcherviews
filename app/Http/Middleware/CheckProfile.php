<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Rules\OldPassword;
use App\Rules\CheckPlusCode;
use App\Rules\CheckCallCode;
use App\Rules\InternationalTel;
use App\Rules\CheckUserPhone;
use App\Rules\CheckUniquePhone;
use App\Rules\CheckPaymentMethods;
use Closure;

class CheckProfile
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
        $userid = Auth::id();
        $rules = [
            'name' => ['required','string','max:255'],
            'bank_name_1' => ['bail',new CheckPaymentMethods,'string','max:20'],
            'bank_no_1' => ['bail','required_with:bank_name_1','nullable','numeric','digits_between:4,30'],
            'bank_name_2' => ['bail','max:20'],
            'bank_no_2' => ['bail','required_with:bank_name_2','nullable','numeric','digits_between:4,30'],
        ];

        if(!empty($request->phone))
        {
          $rules = [
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone' => ['required','numeric','digits_between:6,18',new InternationalTel,new CheckUserPhone($request->code_country,null), new CheckUniquePhone($request->code_country,$request->phone)]
          ];
        }

        if(!empty($request->oldpass) && !empty($request->confpass) || !empty($request->newpass))
        {
          $rules = [
            'oldpass' => ['required','string', new OldPassword],
            'confpass' => ['required','string', 'min:8', 'max:32'],
            'newpass' => [ 'required','string', 'min:8', 'max:32', 'same:confpass'],
          ];
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $err = $validator->errors();
            $error = array(
              'status'=>'error',
              'name'=>$err->first('name'),
              'bank_name_1'=>str_replace('bank name','Nama Bank',$err->first('bank_name_1')),
              'bank_name_2'=>str_replace('bank name','Nama Bank',$err->first('bank_name_2')),
              'bank_no_1'=>str_replace(array('bank no','bank name'),array('No Rekening','Nama Bank'),$err->first('bank_no_1')),
              'bank_no_2'=>str_replace(array('bank no','bank name'),array('No Rekening','Nama Bank'),$err->first('bank_no_2')),
              'code_country'=>$err->first('code_country'),
              'phone'=>$err->first('phone'),
              'oldpass'=>$err->first('oldpass'),
              'confpass'=>$err->first('confpass'),
              'newpass'=>$err->first('newpass')
            );

            return response()->json($error);
        }

        return $next($request);
    }
}
