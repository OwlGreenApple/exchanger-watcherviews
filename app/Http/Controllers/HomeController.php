<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\Orders;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Dispute;
use App\Helpers\Price;
use App\Helpers\Api;
use Carbon\Carbon;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\RegisterController;
use Storage, Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function end_membership()
    {
        return view('auth.trial',['lang'=>new Lang]);
    }

    public function account(Request $request)
    {
        $user = Auth::user();
        $lang = new Lang;
        $membership = Auth::user()->membership;
        $trial = Auth::user()->trial;
        $confirm = $request->segment(2);

        return view('home.account',['user'=>$user,'lang'=>$lang,'pc'=> new Price,'membership'=>$membership,'trial'=>$trial,'conf'=>$confirm]);
    }

    // CONNECT TO WATCHERVIEWS TO OBTAIN WATCHERVIEWS ID
    public function connect_api(Request $request)
    {
        // dd($request->all());
        $email = $request->wt_email;
        $password = $request->wt_pass;

        $api = new Api;
        $wt = $api::connect_watcherviews($email,$password);

        if($wt['err'] == 0)
        {
            // CHECK IF WATCHERVIEWS ID AVAILABLE ON OTHER ID
            self::check_watcherviews_id($wt['id']);
            $user = User::find(Auth::id());
            $user->watcherviews_id = $wt['id'];
            $user->save();
        }

        return response()->json($wt);
    }

    // CHECK IF WATCHERVIEWS ID AVAILABLE ON OTHER ID AND LOGOUT OR SET 0 IF EXIST
    private static function check_watcherviews_id($wt_id)
    {
        $user = User::where('watcherviews_id',$wt_id)->first();

        if(!is_null($user))
        {
            $user_id = $user->id;
            $mb = User::find($user_id);
            $mb->watcherviews_id = 0;
            $mb->save();
        }
    }


    /*WALLET*/
    public function wallet()
    {
        $wt_id = Auth::user()->watcherviews_id;
        $pc = new Price;
        $coin = 0;

        if($wt_id > 0)
        {
            $api = new Api;
            $wt_coin = $api->get_total_coin($wt_id);

            // if err = 1 invalid user id
            // if err = 2 invalid token
            if($wt_coin['err'] == 0)
            {
                $coin = $pc->pricing_format($wt_coin['total_coin']);
            }
            else
            {
                $coin = 0;
            }
        }

        return view('home.wallet',['lang'=>new Lang,'pc'=>new Price,'coin'=>$coin]);
    }

    public function wallet_transaction(Request $request)
    {
        $api = new Api;
        $pc = new Price;
        $coin = $pc::convert_number($request->amount);

        $wallet_option = $request->wallet_option;
        $coin_ammount = $req_coin = $coin;
        $fee = ($coin_ammount * 25)/1000;
        $coin_ammount = $coin_ammount + $fee;

        // WITHDRAW COIN FROM WATCHERVIEWS $wallet_option = 1
        // SEND COIN TO WATCHERVIEWS $wallet_option = 2
        
        $auth = Auth::user();
        $membership = $auth->membership;
        $package = $pc->check_type($membership);

        // COUNT HOW MUCH COIN TO SEND AT WATCHERVIEWS
        if($wallet_option == 1)
        {
          $balance = $coin_ammount;
        }
        else
        {
          $balance = $req_coin;
        }

        $wt_coin = $api::transaction_wt_coin($balance,$auth->watcherviews_id,$wallet_option);
        
        if($wt_coin['err'] == 0)
        {
            // $wt_coin['coin']
            $wallet_coin = self::save_withdrawal($auth->id,$req_coin,$wallet_option,$fee);
            $wt_coin['wallet_coin'] = $wallet_coin;
        }
        
        return response()->json($wt_coin);
    }

    public function display_wallet()
    {
      $wt = Wallet::where('user_id',Auth::id())->get();
      return view('home.wallet-content',['data'=>$wt]);
    }

    // ADD OR SUBTRACT COIN FROM WALLET
    private static function save_withdrawal($id,$coin,$method,$fee)
    {
        // SAVE COIN TO TABLE USER 
        $user = User::find($id);

        // LOGIC TO INSERT INTO TRANSACTION
        $wt = new Wallet;
        $wt->user_id = Auth::id();
        $wt->coin = $coin;
        $wt->fee = $fee;

        if($method == 1)
        {
            $user->coin += $coin;
            $wt->type = 1;
        }
        else
        {
            $coin = $coin + $fee;
            $user->coin -= $coin;
            $wt->type = 2;
        }
        
        try
        {
          $user->save();
          $wt->save();
        }
        catch(queryException $e)
        {
          // 
        }

        return $user->coin;
    }

    // SAVE DISPUTE
    public function save_dispute(Request $request)
    {
      // dd($request->all());
      $role = $request->role;
      $tr_id = $request->tr_id;
      $trs = Transaction::find($tr_id);
      $invoice = $trs->no;

      $dp = new Dispute;
      $dp->user_id = Auth::id();
      $dp->trans_id = $tr_id;
      $dp->role = $role;

      // identity 
      if($request->hasFile('identity'))
      {
        $dir = env('S3path').'/identity/'.explode(' ',trim(Auth::user()->name))[0].'-'.Auth::id();
        $filename = $invoice.'-identity.jpg';
        Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('identity')), 'public');
        $dp->upload_identity = $dir."/".$filename;
      } 

      // proof
      if($request->hasFile('proof'))
      {
        $dir_pf = env('S3path').'/proof/'.explode(' ',trim(Auth::user()->name))[0].'-'.Auth::id();
        $filename_pf = $invoice.'-proof.jpg';
        Storage::disk('s3')->put($dir_pf."/".$filename_pf, file_get_contents($request->file('proof')), 'public');
        $dp->upload_proof = $dir_pf."/".$filename_pf;
      } 

      // mutation
      if($request->hasFile('mutation'))
      {
        $dir_mt = env('S3path').'/mutation/'.explode(' ',trim(Auth::user()->name))[0].'-'.Auth::id();
        $filename_mt = $invoice.'-mutation.jpg';
        Storage::disk('s3')->put($dir_mt."/".$filename_mt, file_get_contents($request->file('proof')), 'public');
        $dp->upload_mutation = $dir_mt."/".$filename_mt;
      } 

      $dp->comments = strip_tags($request->comments);

      try
      {
        $dp->save();
        $res['err'] = 0;
      }
      catch(QueryException $e)
      {
        $res['err'] = $e->getMessage();
      }

      return response()->json($res);
    }

    public function dispute_page()
    {
      return view('dispute');
    }

    public function index()
    {
        return view('home.dashboard',['lang'=>new Lang,'pc'=>new Price]);
    }

    // ORDER FROM BUYER NOT MEMBERSHIP
    public function purchase()
    {
        return view('home.purchasing');
    }

    /*
        trade currently no use due rate / kurs is available
    */
    public function trade()
    {
        return view('home.kurs');
    }

    public function update_profile(Request $request)
    {
        $user = User::find(Auth::id());
        $user->name = strip_tags($request->name);
        $user->bank_name = strip_tags($request->bank_name);
        $user->bank_no = strip_tags($request->bank_no);

        $user->ovo = $request->file('ovo');
        $user->gopay = $request->file('gopay');
        $user->dana = $request->file('dana');

        if($request->newpass !== null)
        {
            $user->password = Hash::make($request->newpass);
        }

        if($request->phone !== null)
        {
            $user->phone_number = $request->code_country.$request->phone;
        }

        try
        {
            $user->save();
            $data = ['success'=>1,'phone'=>$user->phone_number,'msg'=>Lang::get('custom.success')];
        }
        catch(QueryException $e)
        {
            //$e->getMessage()
            $data = ['success'=>0,'msg'=>Lang::get('custom.failed')];
        }

        return response()->json($data);
    }

    public function order_list(Request $request)
    {
      $start = $request->start;
      $length = $request->length;
      $search = $request->search;
      $src = $search['value'];
      $data['data'] = array();

      if($src == null)
      {
         $orders = Orders::where([['user_id',Auth::user()->id]])->orderBy('created_at','desc')->skip($start)->limit($length)->get();                
      }
      else
      {
        if(preg_match("/^[a-zA-Z ]*$/i",$src) == 1)
        {
           $order = Orders::where('package','LIKE','%'.$src.'%');
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $order = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        else
        {
          $order = Orders::where('no_order','LIKE',"%".$src."%");
        }

        $orders = $order->where('user_id',Auth::user()->id)->orderBy('created_at','desc')->get();
      }

      // dd($orders);

      $total = Orders::where('user_id',Auth::user()->id)->count(); 
      $data['draw'] = $request->draw;
      $data['recordsTotal']=$total;
      $data['recordsFiltered']=$total;
      $prc = new Price;

      if($orders->count())
      {
        $no = 1;
        foreach($orders as $order)
        {
          if(($order->proof !== null && $order->status > 1) || ($order->proof == null && $order->status > 1))
          {
            $proof = '-';
          }
          elseif($order->proof == null || $order->status == 0)
          {
            $proof = '<button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#confirm-payment" data-id="'.$order->id.'" data-no-order="'.$order->no_order.'" data-package="'.$order->package.'" data-total="'.$order->total_price.'" data-date="'.$order->created_at.'" style="font-size: 13px; padding: 5px 8px;">'.Lang::get('order.confirm').'
              </button>';
          }
          else
          {
            $proof = '<a class="popup-newWindow" href="'.Storage::disk('s3')->url($order->proof).'">View</a>';
          }

          // STATUS ORDER
          if($order->status==1)
          {
            $status = '<span style="text-success"><b>'.Lang::get('order.process').'</b></span>';
          }
          elseif($order->status==2)
          {
            $status = '<span class="text-primary"><b>'.Lang::get('order.complete').'</b></span>';
          }
          elseif($order->status==3)
          {
            $status = '<span class="text-danger"><b>'.Lang::get('order.cancel').'</b></span>';
          }
          else
          {
            $status = '<span><b>'.Lang::get('order.waiting').'</b></span>';
          }

          if($order->date_confirm == null)
          {
            $date_confirm = '-';
          }
          else
          {
            $date_confirm = Carbon::parse($order->date_confirm)->toDateTimeString();
          }
          
          $data['data'][] = [
            0=>$order->no_order,
            1=>$order->package,
            2=>Lang::get('custom.currency')." ".$prc->pricing_format($order->price),
            3=>Lang::get('custom.currency')." ".$prc->pricing_format($order->total_price),
            4=>Carbon::parse($order->created_at)->toDateTimeString(),
            5=>$date_confirm,
            6=>$order->desc,
            7=>$proof,
            8=>$status
          ];
        }
      }
     
      echo json_encode($data);
    }

    //upload bukti TT 
      public function confirm_payment_order(Request $request)
      {
        $user = Auth::user();
        //konfirmasi pembayaran user
        $order = Orders::find($request->id_confirm);
        $folder = $user->email.'/buktibayar';
        $celeb = null;
        $pathUrl = str_replace(url('/'), '', url()->previous());

        if(strlen($request->keterangan) > 300)
        {
            return redirect($pathUrl)->with("error", Lang::get('order.max_notes'));
        }

        if(env('APP_ENV') == 'local')
        {
            $celeb = 'exchanger';
        }

        if($order->status==0)
        {
          $order->status = 1;

          if($request->hasFile('buktibayar'))
          {
            $dir = $celeb.'/bukti_bayar/'.explode(' ',trim($user->name))[0].'-'.$user->id;
            $filename = $order->no_order.'.jpg';
            Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('buktibayar')), 'public');
            $order->proof = $dir."/".$filename;
            
          } else {
            return redirect($pathUrl)->with("error", Lang::get('order.upload_first'));
          }  
          $order->notes = $request->keterangan;
          $order->save();
        } 
        else 
        {
            return redirect($pathUrl)->with("error", Lang::get('order.reject'));
        }

        return view('order.thankyou-confirm-payment',['lang'=> new Lang]);
      }

/**/    
}
