<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use App\Models\Orders;
use App\Models\User;
use App\Models\Transaction;
use App\Helpers\Price;
use App\Helpers\Api;
use Carbon\Carbon;
use App\Http\Controllers\OrderController;
use Storage;

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

    public function connect_api()
    {
        $membership = Auth::user()->membership;
        $trial = Auth::user()->trial;

        if($membership == 'free' && $trial == 0)
        {
            return view('auth.trial',['lang'=>new Lang]);
        }
        return view('home.connect_api',['lang'=>new Lang,'pc'=>new Price]);
    }

    public function index()
    {
        return view('home.buy',['lang'=>new Lang,'pc'=>new Price]);
    }

    /*
        trade currently no use due rate / kurs is available
    */
    public function trade()
    {
        return view('home.kurs');
    }

    public function buying_page()
    {
        return view('home.buy',['lang'=>new Lang,'pc'=>new Price]);
    }

    public function selling_page()
    {
        return view('home.sell',['lang'=>new Lang,'pc'=>new Price]);
    }

    public function history_transaction()
    {
        $tr = Transaction::where('user_id',Auth::id())->get();
        return view('home.transaction',['lang'=>new Lang,'data'=>$tr]);
    }

    public function wallet()
    {
        $membership = Auth::user()->membership;
        $trial = Auth::user()->trial;

        if($membership == 'free' && $trial == 0)
        {
            return view('auth.trial',['lang'=>new Lang]);
        }
        return view('home.wallet',['lang'=>new Lang,'pc'=>new Price]);
    }

    public function get_watcherviews_coin(Request $request)
    {
        $email = $request->wt_email;
        $password = $request->wt_pass;

        $api = new Api;
        $pc = new Price;
        $auth = Auth::user();
        $membership = $auth->membership;
        $package = $pc->check_type($membership);

        // COUN HOW MUCH COIN IN WALLET
        $user_coin = $auth->coin;
        $max_coin = $package['max_coin'] - $user_coin;

        $wt_coin = $api::get_watcerviews_coin($email,$password,$max_coin);

        if($wt_coin['err'] == 0)
        {
            // SAVE COIN TO TABLE USER
            $user = User::find($auth->id);
            $user->coin += $wt_coin['coin'];
            $user->save();

            // LOGIC TO INSERT INTO TRANSACTION
            $dt = Carbon::now();
            $str = 'WD-'.$dt->format('ymd').'-'.$dt->format('Hi');
            $logic = new OrderController;
            $no_transaction = $logic::autoGenerateID(new \App\Models\Transaction, 'no', $str, 3, '0');

            $data = [
                'no'=>$no_transaction,
                'amount'=>$wt_coin['coin'],
                'type'=>3,
            ];

            $pc::transaction($data);
        }

        return response()->json($wt_coin);
    }

    public function profile()
    {
        $user = Auth::user();
        $lang = new Lang;
        return view('home.profile',['user'=>$user,'lang'=>$lang]);
    }

    public function update_profile(Request $request)
    {
        $user = User::find(Auth::id());
        $user->name = strip_tags($request->name);
        $user->bank_name = strip_tags($request->bank_name);
        $user->bank_no = strip_tags($request->bank_no);

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

    public function order()
    {
        $lang = ['lang'=> new Lang];
        return view('home.order',$lang);
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
