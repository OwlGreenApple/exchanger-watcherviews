<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\LoginController as Login;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\RegisteredEmail;
use App\Rules\CheckPlusCode;
use App\Rules\CheckCallCode;
use App\Rules\InternationalTel;
use App\Rules\CheckUserPhone;
use App\Rules\CheckUserId;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cookie;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        /*return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);*/

        return Validator::make($data, [
            'username' => ['required','string','min:3','max:50'],
            'email' => ['required','string', 'email', 'max:50', 'unique:users'],
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone' => ['required','numeric','digits_between:6,18',new InternationalTel,new CheckUserPhone($data['code_country'],null)],
            'referral'=>[new CheckUserId]
        ]);
    }

     public function showRegistrationForm()
     {
        
        $referral = [
          'ref_name'=>Cookie::get('referral'),
          'ref_id'=>Cookie::get('refid')
        ];
        return view('auth.register',$referral);
     }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $generated_password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'),0,10);

        //if normal register
        if(!isset($data['referral']) ||$data['referral'] == null || empty($data['referral']) || $data['referral'] =="")
        {
           $data['referral'] = 0;
        }

        $user = User::create([
          'name' => strip_tags($data['username']),
          'email' => strip_tags($data['email']),
          'phone'=>strip_tags($data['code_country'].$data['phone']),
          'code_country'=>strip_tags($data['data_country']),
          'password' => Hash::make($generated_password),
          'gender'=>strip_tags($data['gender']),
          'referral_id'=>strip_tags($data['referral']),
          'date_bonus'=> Carbon::createFromFormat('Y-m-d H:i:s', '1970-01-01 00:00:00')
        ]);

        if(env('APP_ENV') == 'production'):
          Mail::to($data['email'])->send(new RegisteredEmail($generated_password,$data['username']));
        endif;
         
        return $user;
    }

    //register ajax validator
    protected function ajax_validator(array $data)
    {
        $validator = $this->validator($data);

        $err = $validator->errors();
        if($validator->fails() == true)
        {
            $errors = [
              'success'=>0,
              'username'=>$err->first('username'),
              'email'=>$err->first('email'),
              'code_country'=>$err->first('code_country'),
              'phone'=>$err->first('phone'),
              'referral'=>$err->first('referral'),
            ];
            return response()->json($errors);
        }
        else
        {
            if(!isset($data['referral']))
            {
              $data['referral'] = null;
            }
            return $this->register_ajax($data);
        }
    }

    //REGISTER VIA AJAX
    protected function register_ajax(array $data)
    {
        $signup = $this->create($data);
        $order = null;

        //LOGIN USER ID
        Auth::loginUsingId($signup->id);

        //COINS GIFT DAILY
        $log = new Login;
        $log->get_daily_bonus();

        //COINS GIFT REFERRAL
        if($data['referral'] <> null)
        {
          $this->ref_coins_gift($data['referral'],$signup);
        }

        //DELETE COOKIE REFERRAL
        if(Cookie::get('referral') <> null || Cookie::get('refid') <> null)
        {
            $this->delCookie();
        }
        return response()->json([
            'success' => 1,
            'email' => $signup->email,
        ]);
    }

    //REGISTER USERS -- ALL REGISTER VIA AJAX
    public function register(Request $request)
    {   
        $req = $request->all();
        if($request->ajax() == true)
        {
          return $this->ajax_validator($req,$request);
        }
        else
        {
          return redirect('login');
        }
    }

    //DELETE REFERRAL COOKIE WHEN REFERRAL USER SUCCESSFULLY REGISTERED
    public function delCookie()
    {
      Cookie::queue(Cookie::forget('referral'));
      Cookie::queue(Cookie::forget('refid'));
      return;
    }

    private function ref_coins_gift($userid,$newuser)
    {
        $coins = 0;
        $user = User::find($userid);
        $referal = User::where('referral_id',$userid)->get()->count();
        if($referal == 0)
        {
           return;
        }

        $formula = $referal % 5;

        if($formula == 1)
        {
          $coins = 10000;
        }
        elseif($formula == 2)
        {
          $coins = 12000;
        }
        elseif($formula == 3)
        {
          $coins = 14000;
        }
        elseif($formula == 4)
        {
          $coins = 16000;
        }
        else
        {
          $coins = 18000;
        }

        try{
          $user->credits += $coins;
          $user->save();

          //note to database transaction coins.
          $trans = new Transaction;
          $trans->user_id = $userid;
          $trans->debit = $coins;
          $trans->source = "referral-gift-user-".$newuser->name."-".$newuser->id;
          $trans->save();
        }
        catch(QueryException $e)
        {
          //$e->getMessage();
        }
    }

/* end class */
}
