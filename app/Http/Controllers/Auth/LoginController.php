<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use App\Models\User;
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
        self::check_banned($request);
        if($request->remember == 1)
        {
            $this->setCookie($request->email,$request->password);
        }
        else 
        {
            $this->delCookie($request->email,$request->password);
        }
    }

    // CHECK BANNED USER
    private static function check_banned(Request $request)
    {
        if(Auth::check() == true && (Auth::user()->status == 0))
        {
            $status = Auth::user()->status;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if($request->ajax() == false && $status == 0)
            {
                return redirect()->route('login')->with('error', Lang::get('auth.banned'));
            }
        }
    }

    public function loginAjax(Request $request)
    {
        // dd($request->all());
        $email = $request->email;
        $password = $request->password;

        // check email / password valid / tidak
        if(Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) 
        {
            $user = User::where('email',$email)->first();
            if($user->status == 0)
            {
                self::check_banned($request);
                $ret = [
                    'success' => false,
                    'message' => Lang::get('auth.banned'),
                ];
            }
            else
            {
                $ret = [
                    'success' => 1,
                    'email' => $email,
                ];
            }
            return response()->json($ret);
        } else {
            return response()->json([
                'success' => false,
                'message' => Lang::get('auth.credential')
            ]);
        }
    }

    public function redirectTo() {
      if(Auth::check() == true)
      {
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
      else
      {
         return '/login'; 
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
