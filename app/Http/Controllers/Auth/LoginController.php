<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

   /* public function authenticated(){
      if ($request->ajax()) {
            Auth::loginUsingId($user->id);
            return response()->json([
                'success' => 1,
                'email' => $request->email,
            ]);
        }
    }*/

    public function loginAjax(Request $request)
    {
        $email = strip_tags($request->email);
        $password = strip_tags($request->password);

        if($request->remember == 1)
        {
            $this->setCookie($email,$password);
        }
        else {
            $this->delCookie($email,$password);
        }

        if(Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) 
        {
            $user = Auth::user();
            
            return response()->json([
                'success' => 1,
                'email' => $request->email,
                /*'price'=>$price,
                'total'=>number_format($upgrade_price),*/
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Credential Account'
            ]);
        }
    }

    private function setCookie($email,$password)
    {
        if(!empty($email) && !empty($password))
        {
            Cookie::queue(Cookie::make('email', $email, 1440*7));
            Cookie::queue(Cookie::make('password', $password, 1440*7));
        } else {
            return redirect()->route('login');
        }
    }

    private function delCookie($cookie_email,$cookie_pass)
    {
        if(!empty($cookie_email) && !empty($cookie_pass))
        {
            Cookie::queue(Cookie::forget('email'));
            Cookie::queue(Cookie::forget('password'));
        } else {
            return redirect()->route('login');
        }
    }

/* end AUTH */
}
