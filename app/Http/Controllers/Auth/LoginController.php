<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Cookie,Session;


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

    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    public function redirectTo() {
      $role = Auth::user()->is_admin; 
      switch ($role) {
        case 0:
          return '/home';
          break;
        case 1:
          return '/account';
          break; 

        default:
          return '/home'; 
        break;
      }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request,$user)
    {
        if($request->remember == 1)
        {
            $this->setCookie($request->email,$request->password);
        }
        else 
        {
            $this->delCookie($request->email,$request->password);
        }
    }

    public function loginAjax(Request $request)
    {
        // dd($request->all());
        $email = $request->email;
        $password = $request->password;

        if(Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) 
        {
            $user = Auth::user();
            return response()->json([
                'success' => 1,
                'email' => $email,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => Lang::get('auth.credential')
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

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect('checkout');
    }

/**/    
}
