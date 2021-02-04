<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\User;
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

    //IF USER SUCCESSFULLY LOGIN
    public function authenticated()
    {
       return $this->get_daily_bonus();
    }

    //CHECK USER LOGIN BONUS DAILY
    public function get_daily_bonus()
    {
      $user = Auth::user();
      // $already_get_bonus = false; 
      $total_login = $user->total_login;
      $get_bonus = Carbon::parse($user->date_bonus)->setTime(0, 0, 0); //set time to 00:00:00

      if (is_null($user->updated_at)){
        $last_activity = Carbon::now()->subDays(7);
      }
      else {
        $last_activity = Carbon::createFromFormat('Y-m-d H:i:s', $user->updated_at);
      }

      //determine user get bonus or not
     /* if ( $last_activity->diffInDays(Carbon::now()) >= 1 && $last_activity->diffInDays(Carbon::now()) < 2  ) {
        $already_get_bonus = false; // blm dapat bonus
        $user->date_bonus = Carbon::now();
      }*/
      if ($last_activity->diffInDays(Carbon::now()) >= 2) {
        $total_login = 0;
        $user->date_bonus = Carbon::now();
      }
    /*  else if ( $last_activity->diffInDays(Carbon::now()) < 1  ) 
      {
        $already_get_bonus = true;
      }*/

      //check if user last login eligible to get bonus
      //TO PREVENT IF USER LOGIN AND THEN LOGOUT SEVERAL TIMES AND GET BONUSES
      if($get_bonus->diffInDays(Carbon::now()) > 0)
      {
          //SET COINS GIFT
         $total_login++;
         //SET BACK TO 1 IF TOTAL LOGIN REACH MAX TERMS
         if($total_login > 7)
         {
            $total_login = 1;
         }  
         $this->giftDaily($total_login); 
      }
      else
      {   
          //if user login 2 days after last login then total login become 1
         /* $user->total_login = $total_login;
          $user->save();  */
          return redirect('home');
      }

      try 
      {
        $user->total_login= $total_login;
        $user->date_bonus = Carbon::now(); //noted if user get bonuses already
        $user->save();  
        return redirect('home');
      }
      catch (QueryException $e) {
        /*$res['error'] = true;
        $res['message'] = $e->getMessage();*/
        return redirect('login');
      }
    }

    private function giftDaily($total_login)
    {
      $user = User::find(Auth::id());
      $credit = 0;

      if($total_login == 1)
      {
        $credit = 2000;
      }
      elseif($total_login == 2)
      {
        $credit = 4000;
      }
      elseif($total_login == 3)
      {
        $credit = 6000;
      }
      elseif($total_login == 4)
      {
        $credit = 8000;
      }
      elseif($total_login == 5)
      {
        $credit = 10000;
      }
      elseif($total_login == 6)
      {
        $credit = 15000;
      }
      elseif($total_login == 7)
      {
        $credit = 20000;
      }

      try
      {
        $user->credits += $credit;
        $user->save();

        $trans = new Transaction;
        $trans->user_id = Auth::id();
        $trans->debit = $credit;
        $trans->source = "daily-".$total_login."-bonus";
        $trans->save();
      }
      catch(QueryException $e)
      {
        //$e->getMessage();
      }
    }

/* end AUTH */
}
