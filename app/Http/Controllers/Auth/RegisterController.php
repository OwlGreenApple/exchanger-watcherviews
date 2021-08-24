<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use App\Rules\CheckPlusCode;
use App\Rules\CheckCallCode;
use App\Rules\InternationalTel;
use App\Rules\CheckUserPhone;
use App\Rules\CheckUniquePhone;
use App\Mail\RegisteredEmail;

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
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }*/
    
    public function showRegistrationForm()
    {
        return view('auth.register',['lang'=> new Lang]);
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
        $name = $data['username'];
        $phone = $data['code_country'].$data['phone'];

        $col = [
          'name' => strip_tags($data['username']),
          'email' => strip_tags($data['email']),
          'phone_number'=>strip_tags($data['code_country'].$data['phone']),
          'password' => Hash::make($generated_password),
          'gender'=>strip_tags($data['gender']),
        ];

        // PREMIUM MEMBERSHIP
        if(isset($data['membership']))
        {
            $col['membership'] = strip_tags($data['membership']);
            $col['trial'] = 0;
        }

        Mail::to($data['email'])->send(new RegisteredEmail($generated_password,$data['username']));

        return User::create($col);
    }

    protected function ajax_validator(array $data)
    {
        $validator = Validator::make($data, [
            'username' => ['required','string','max:255'],
            'email' => ['required','string', 'email', 'max:255', 'unique:users'],
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone' => ['required','numeric','digits_between:6,18',new InternationalTel,new CheckUserPhone($data['code_country'],null), new CheckUniquePhone($data['code_country'],$data['phone'])]
        ]);

        $err = $validator->errors();
        if($validator->fails() == true)
        {
            $errors = [
              'success'=>0,
              'username'=>$err->first('username'),
              'email'=>$err->first('email'),
              'code_country'=>$err->first('code_country'),
              'phone'=>$err->first('phone'),
            ];
            return response()->json($errors);
        }
        else
        {
            return $this->register_ajax($data);
        }
    }

    public function register(Request $request)
    {   
        $req = $request->all();
        if($request->ajax() == true)
        {
          return $this->ajax_validator($req,$request);
        }

        $signup = $this->create($req);
        $order = null;
        
        Auth::loginUsingId($signup->id);
        if($request->ajax()) {
            return response()->json([
                    'success' => 1,
                    'email' => $signup->email,
            ]);
        }
        else {
          return redirect('home');
        }
    }

    //REGISTER VIA AJAX
    protected function register_ajax(array $data)
    {
        $signup = $this->create($data);
        $order = null;

        Auth::loginUsingId($signup->id);
        return response()->json([
            'success' => 1,
            'email' => $signup->email,
        ]);
    }

/**/
}
