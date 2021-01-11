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
use App\Mail\RegisteredEmail;
use App\Rules\CheckPlusCode;
use App\Rules\CheckCallCode;
use App\Rules\InternationalTel;
use App\Rules\CheckUserPhone;
use Illuminate\Http\Request;

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
            'username' => ['required','string','max:255'],
            'email' => ['required','string', 'email', 'max:255', 'unique:users'],
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone' => ['required','numeric','digits_between:6,18',new InternationalTel,new CheckUserPhone($data['code_country'],null)]
        ]);
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

        $user = User::create([
          'name' => strip_tags($data['username']),
          'email' => strip_tags($data['email']),
          'phone'=>strip_tags($data['code_country'].$data['phone']),
          'code_country'=>strip_tags($data['data_country']),
          'password' => Hash::make($generated_password),
          'gender'=>strip_tags($data['gender']),
        ]);

        Mail::to($data['email'])->send(new RegisteredEmail($generated_password,$data['username']));
         
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
            ];
            return response()->json($errors);
        }
        else
        {
            return $this->register_ajax($data);
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

    public function register(Request $request)
    {   
        $req = $request->all();
        
        //REGISTER VIA AJAX
        if($request->ajax() == true)
        {
          return $this->ajax_validator($req,$request);
        }

        //REGISTER VIA FORM
        $signup = $this->create($req);
        $order = null;
        
        Auth::loginUsingId($signup->id);
        if ($request->ajax()) {
            return response()->json([
                'success' => 1,
                'email' => $signup->email,
            ]);
        }
        else {
          return redirect('home');
        }
    }

/* end class */
}
