<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController as Odc;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\User;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Dispute;
use App\Models\Transaction;
use App\Models\Kurs;
use App\Models\Event;
use App\Models\Wallet;
use App\Models\Atcoupons;
use App\Helpers\Api;
use App\Helpers\Price;
use App\Helpers\Messages;
use App\Mail\NotifyEmail;
use App\Mail\WarningEmail;
use App\Imports\AtCouponsImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Storage, Validator, DB;

class AdminController extends Controller
{

    public function dispute()
    {
      return view('admin.dispute.index');
    }

    public function display_dispute(Request $request)
    {
       $dp = Transaction::leftJoin('disputes AS dp','transactions.buyer_dispute_id','=','dp.id')
            ->leftJoin('disputes AS ds','transactions.seller_dispute_id','=','ds.id')
            ->leftJoin("users AS us",'us.id','=','dp.user_id')
            ->leftJoin("users AS ur",'ur.id','=','ds.user_id')
            ->leftJoin("users AS tb",'tb.id','=','transactions.buyer_id')
            ->leftJoin("users AS ts",'ts.id','=','transactions.seller_id')
            ->select('dp.upload_identity',
              'dp.upload_proof AS buyer_proof',
              'dp.upload_mutation',
              'dp.created_at AS buyer_dispute_date',
              'ds.upload_proof AS seller_proof',
              'ds.created_at AS seller_dispute_date',
              'us.name AS buyer_name',
              'ur.name AS seller_name',
              'tb.status AS buyer_status',
              'ts.status AS seller_status',
              'transactions.no AS invoice','transactions.date_buy','transactions.id','transactions.status','transactions.seller_dispute_id','transactions.buyer_dispute_id','transactions.buyer_id','transactions.seller_id','transactions.updated_at'
            )
            ->where('transactions.seller_dispute_id','>',0)->orWhere('transactions.buyer_dispute_id','>',0)->orderBy('transactions.id','desc')->get();

      // dd($dp);

       return view('admin.dispute.content',['data'=>$dp]);
    }

    public function dispute_notify_user(Request $request)
    {
      $data_buyer = $request->data_buyer;  
      $data_seller = $request->data_seller;  
      $data_trid = $request->data_trid;  

      $tr = Transaction::find($data_trid);
      if(is_null($tr))
      {
        return response()->json(['err'=>1]);
      }

      $msg ='';
      $msg .='Sehubungan dengan invoice : *'.$tr->no.'*'."\n";
      $msg .='maka admin mengundang anda untuk menyelesaikan dispute ini melalui chat'."\n";
      $msg .='dengan link di bawah ini : '."\n\n";
      $msg .=url('chat').'/'.$tr->id."\n\n"; 
      $msg .='Terima Kasih'."\n";
      $msg .='Team Exchanger';

      $users = User::whereIn('id',[$data_buyer,$data_seller])->get();

      if($users->count() > 0)
      {
        foreach ($users as $row) 
        {
          $data = [
              'message'=>$msg,
              'phone_number'=>$row->phone_number,
              'email'=>$row->email,
              'obj'=>new WarningEmail($tr->no,$tr->id,'Chat Dispute'),
          ];
          
          $this->notify_user($data);
        }
      }
    }

    public function dispute_notify(Request $request)
    {
      $user_id = $request->user_id;
      $trans_id = $request->trans_id;
      $role = $request->role;

      /*
        role 1 = buyer
        role 2 = seller
      */

      $user = User::find($user_id);
      if(is_null($user))
      {
        return response()->json(['err'=>1]);
      }

      $tr = Transaction::find($trans_id);
      if(is_null($tr))
      {
        return response()->json(['err'=>1]);
      }

      $msg ='';
      $msg .='Invoice anda : *'.$tr->no.'*'."\n";
      $msg .='telah di dispute'."\n";

      if($role == 1)
      {
        $msg .='Jika anda yakin bahwa anda sudah membayar penjual, maka silahkan menyanggah disini'."\n\n";

        $url_confirm = '-';
        $url_dispute = url('buyer-dispute').'/'.$tr->id;
      }
      else
      {
        $url_confirm = url('sell-confirm').'/'.$tr->no;
        $url_dispute = url('seller-dispute').'/'.$tr->id;

        $msg .='Jika anda belum konfirmasi silahkan konfimasi di sini :'."\n\n";
        $msg .=$url_confirm."\n\n";
        $msg .='Jika anda yakin bahwa pembeli belum membayar anda, maka silahkan menyanggah disini'."\n\n";
      }
      
      $msg .=$url_dispute."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Exchanger';

      $data = [
          'message'=>$msg,
          'phone_number'=>$user->phone_number,
          'email'=>$user->email,
          'obj'=>new NotifyEmail($tr->no,$url_confirm,$url_dispute,$role),
      ];

      // dd($data);
      return $this->notify_user($data);
    }

    // GLOBAL NOTIFCATION USER THROUGH wa AND EMAIL
    public function notify_user(array $data)
    {
      $notif = Notification::all()->first();
      $admin_id = $notif->admin_id;
      $api = new Api;

      $api->send_wa_message($admin_id,$data['message'],$data['phone_number']);
      Mail::to($data['email'])->send($data['obj']);
    }

    public function dispute_user(Request $request)
    {
      $trans_id = $request->data_trid;
      $data_buyer = $request->data_buyer;
      $data_seller = $request->data_seller;
      $data_win = $request->data_win;

      if($data_win == 1)
      {
        return $this->buyer_win($trans_id,$data_buyer,$data_seller);
      }
      elseif($data_win == 2)
      {
        return $this->seller_win($trans_id,$data_buyer,$data_seller);
      }
      elseif($data_win == 0)
      {
        return $this->end_dispute($trans_id,$data_buyer,$data_seller);
      }
      else
      {
        return response()->json(['err'=>1]);
      }

    }

    // IF ADMIN DECIDE BUYER WIN
    public function buyer_win($trans_id,$data_buyer,$data_seller,$end_dispute = null)
    {
      $tr = Transaction::find($trans_id);
    
      // BUYER DISPUTE
      if(!is_null($tr))
      {
        $seller_id = $tr->seller_id;
        $buyer_id = $tr->buyer_id;

        // SELLER GET WARNING
        $this->warning_user($seller_id,$tr);

        try
        {
          // GIVING COIN FROM SELLER TO BUYER
          $coin = $tr->amount;
          $user = User::find($buyer_id);
          $user->coin += $coin;
          $user->save();

          if($end_dispute == null)
          {
            return self::change_transaction($tr,3);
          }
        }
        catch(QueryException $e)
        {
          return response()->json(['err'=>1]);
        }
        
      }
      else
      {
        return response()->json(['err'=>1]);
      }
    }

    // IF ADMIN DECIDE SELLER WIN
    public function seller_win($trans_id,$data_buyer,$data_seller,$end_dispute = null)
    {
      $return_coin = 0;
      $tr = Transaction::find($trans_id);
    
      // SELLER DISPUTE
      if(!is_null($tr))
      {
        $seller_id = $tr->seller_id;
        $buyer_id = $tr->buyer_id;

        // BUYER GET WARNING
        $this->warning_user($buyer_id,$tr);

        $wallet = new Wallet;
        $wallet->user_id = $seller_id;
        $wallet->type = 3;
        $wallet->coin = $tr->amount;
        $wallet->fee = $tr->coin_fee;

        // RETURNING COIN TO WALLET
        $return_coin = $tr->coin_fee + $tr->amount;
        $user = User::find($seller_id);
        $user->coin += $return_coin;

        try
        {
          $wallet->save();
          $user->save();

          if($end_dispute == null)
          {
            return self::change_transaction($tr,5);
          }
        }
        catch(Queryexception $e)
        {
          return response()->json(['err'=>$e->getMessage()]);
        }
      }
      else
      {
        // TRANSACTION ID NOT AVAILABLE
        return response()->json(['err'=>1]);
      }
    }

    // IF ADMIN DECIDE TO BLAME ON BOTH
    public function end_dispute($trans_id,$data_buyer,$data_seller)
    {
      $tr = Transaction::find($trans_id);
    
      if(!is_null($tr))
      {
        $seller_id = $tr->seller_id;
        $buyer_id = $tr->buyer_id;

        // BOTH WIN GETS WARNING
        $this->buyer_win($trans_id,$data_buyer,$data_seller,true);
        $this->seller_win($trans_id,$data_buyer,$data_seller,true);
        return self::change_transaction($tr,6,0);
      }
      else
      {
        // TRANSACTION ID NOT AVAILABLE
        return response()->json(['err'=>1]);
      }
    }

    // WARNING USER
    public function warning_user($user_id,$tr)
    {
        $total_warning = $total_suspend = $nextId = 0;
        $evt = null;
        $user = User::find($user_id);
        $msg = new Messages;
        $notif = new Event;
        $previous_warning = $user->warning;
        $previous_suspend = $user->suspend;
        $notif->user_id = $user->id;

        // WARNING
        if($user->status > 0 && $user->warning < 1)
        {
          $notif->event_name = 'Warning Akun';
          $notif->message = $msg::warning_message($tr->no);
          $notif->url = '-';
          $notif->save();

          $next_id = $notif->id;
          $evt = Event::find($next_id);
          $evt->url = 'event/'.$next_id;
          $evt->save();

          $user->warning++;
          $msg = $msg::message_warning($tr->no);
          $notification_email = new WarningEmail($tr->no,null,'Warning Email');
        }

        // SUSPEND
        if($previous_warning > 0 && $previous_suspend == 0)
        {
          $user->suspend++;
          $user->status = 3; //suspend user
          $total_suspend = $user->suspend;
          $user->suspend_date = Carbon::now();

          $notif->event_name = 'Akun Ter-Suspend';
          $notif->message = $msg::suspend_message($tr->no);
          $notif->url = '-';
          $notif->save();

          $next_id = $notif->id;
          $evt = Event::find($next_id);
          $evt->url = 'event/'.$next_id;
          $evt->save();

          // send wa notify here
          $notification_email = new WarningEmail($tr->no,null,'Suspend Email');
          $msg = $msg::message_suspend($tr->no);
        }
        
        // BANNED
        if($previous_suspend > 0)
        {
          $user->status = 0;
          $user->suspend_date = Carbon::now();
          $notification_email = new WarningEmail($tr->no,null,'Banned Email');
          $msg = $msg::message_ban($tr->no);
        }

        // SEND WA AND MAIL AS NOTIFICATION
         $data = [
            'message'=>$msg,
            'phone_number'=>$user->phone_number,
            'email'=>$user->email,
            'obj'=>$notification_email,
        ];
        $this->notify_user($data);

        try
        {
          $user->save();
        }
        catch(QueryException $e)
        {
          return response()->json(['err'=>1,['msg'=>$e->getMessage()]]);
        }
    }

    // RETURNING USER COIN ON THEIR WALLET AND CHANGE TRANSACTION STATUS
    public static function change_transaction($tr,$status)
    {
      try
      {
        $tr->status = $status;
        $tr->save();
        return response()->json(['err'=>0]);
      }
      catch(QueryException $e)
      {
        return response()->json(['err'=>1,['msg'=>$e->getMessage()]]);
      }

    }

    // DISPLAY TRADE
    public function trade()
    {
      $kurs = Kurs::all();
      $data = array();
      foreach($kurs as $row)
      {
        $data[Carbon::parse($row->created_at)->toDateTimeString()] = $row->kurs;
      }
      return view('admin.order.trade',['data'=>$data]);
    }

    public function save_rate(Request $request)
    {
      $kurs = new Kurs;
      $kurs->kurs = $request->kurs;

      try
      {
        $kurs->save();
        $ret['msg'] = 'Data berhasil disimpan';
      }
      catch(QueryException $e)
      {
        $ret['success'] = 'error';
        $ret['msg'] = $e->getMessage();;
      }

      return response()->json($ret);
    }

    public function index()
    {
    	return view('admin.order.index');
    }

    /** ACTIVTEMPLATE COUPON **/
    public function atm_coupons()
    {
      return view('admin.order.atm-coupons');
    }

    public function atm_coupons_table()
    {
      $atm = Atcoupons::leftJoin('users','users.id','=','atcoupons.user_id')->select("atcoupons.*","users.name")->orderBy('id','desc')->get();
      return view('admin.order.atm-coupons-table',['data'=>$atm]);
    }

    public function import_coupon(Request $request)
    {
      $file = $request->file('upload_coupon');
      $data = Excel::toArray(new AtCouponsImport,$file);
      $voucher_1 = $voucher_2 = array();

      foreach($data as $col)
      {
        $voucher_1 = array_column($col,0);
        $voucher_2 = array_column($col,1);
      }

      // voucher 100.000
      if(count($voucher_1) > 0)
      {
        array_shift($voucher_1);
        self::import_voucher_activtemplate($voucher_1,1);
      }

      // voucher 200.000
      if(count($voucher_2) > 0)
      {
        array_shift($voucher_2);
        self::import_voucher_activtemplate($voucher_2,2);
      }

      return response()->json(['response'=>1]);
    }

    private static function import_voucher_activtemplate($voucher,$type)
    {
      foreach($voucher as $row):
        $check = Atcoupons::where('code',$row)->first();

        if(is_null($check))
        {
          $at = new Atcoupons;
          $at->user_id = 0;
          $at->code = $row;
          $at->date_used = null;
          $at->type = $type;
          $at->is_used = 0;
          $at->save();
        }
        else
        {
          continue;
        }
      endforeach;
    }

    /*** -- USER -- ***/
    public function user_list()
    {
      return view('admin.order.user');
    }

    public function fetch_user(Request $request)
    {
      $start = $request->start;
      $length = $request->length;
      $search = $request->search;
      $src = $search['value'];
      $data['data'] = array();

      if($src == null)
      {
         $db = User::orderBy('id','desc')->skip($start)->limit($length)->get();
      }
      else
      {
        if(preg_match("/^ACT[a-zA-Z0-9]/i",$src))
        {
          $db = Orders::where('no_order','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[a-zA-Z]/i",$src))
        {
          $db = Orders::where('package','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $db = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        else
        {
          $db = Orders::where('notes','LIKE',"%".$src."%");
        }

        $db = $db->orderBy('created_at','desc')->get();
      }

      $total = User::count(); //use this instead of ->count(), this cause error when in case large amount data.
      $data['draw'] = $request->draw;
      $data['recordsTotal']=$total;
      $data['recordsFiltered']=$total;
      $api = new Price;

      if($db->count())
      {
        $no = 1;
        foreach($db as $row)
        {
          
          if($row->status < 1)
          {
            $btn = '<span class="text-danger">Banned</span>';
          }
          else
          {
            $btn = '<button type="button" id="'.$row->id.'" class="btn btn-danger btn-sm ban">Ban User</button>';
          }

          $data['data'][] = [
            0=>$no++,
            1=>$row->name,
            2=>$row->email,
            3=>$row->phone_number,
            4=>$row->membership,
            5=>$row->trial,
            6=>$row->end_membership,
            7=>Carbon::parse($row->created_at)->toDateTimeString(),
            8=>$btn,
          ];
        }
      }
     
      echo json_encode($data);
    }

    public function ban_user(Request $request)
    {
      $user = User::find($request->id);
      if(is_null($user))
      {
        $data['error'] = 2;
        $data['msg'] = 'User tidak ada';
        return response()->json($data);
      }
      
      try
      {
        $user->status = 0;
        $user->save();
        $data['error'] = 0;
      }
      catch(queryException $e)
      {
        $data['error'] = 1;
        $data['msg'] = $e->getMessage();
      }
      return response()->json($data);
    }

    /*** ORDER ***/
    public function order(Request $request)
    {
      $start = $request->start;
      $length = $request->length;
      $search = $request->search;
      $src = $search['value'];
      $data['data'] = array();

      if($src == null)
      {
         $orders = Orders::orderBy('created_at','desc')->skip($start)->limit($length)->get();
      }
      else
      {
        if(preg_match("/^ACT[a-zA-Z0-9]/i",$src))
        {
          $order = Orders::where('no_order','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[a-zA-Z]/i",$src))
        {
          $order = Orders::where('package','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $order = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        else
        {
          $order = Orders::where('notes','LIKE',"%".$src."%");
        }

        $orders = $order->orderBy('created_at','desc')->get();
      }

      $total = Orders::count(); //use this instead of ->count(), this cause error when in case large amount data.
      $data['draw'] = $request->draw;
      $data['recordsTotal']=$total;
      $data['recordsFiltered']=$total;
      $api = new Price;

      if($orders->count())
      {
        foreach($orders as $row)
        {
          if($row->notes !== null)
          {
          	$notes = '<textarea>'.$row->notes.'</textarea>';
          }
          else
          {
          	$notes = '-';
          }

          if($row->status == 0)
          {
            $confirm = '<b class="text-default">Menunggu Konfirmasi</b>';
          }
          elseif($row->status == 1)
          {
          	$confirm = '<button type="button" class="btn btn-info btn-sm confirm" data-toggle="modal" data-target="#confirm_popup" data-id="'.$row->id.'">Konfirmasi</button> <button type="button" class="btn btn-danger btn-sm cancel" data-toggle="modal" data-target="#cancel_popup" data-id="'.$row->id.'">Batal</button>';
          }
          elseif($row->status == 2)
          {
            $confirm = '<b class="text-success">Terkonfirmasi</b>';
          }
          elseif($row->status == 3)
          {
          	$confirm = '<b class="text-danger">Batal</b>';
          }
          else
          {
            $confirm = '<b class="text-danger">Batal oleh system</b>';
          }

          // PROOF
          if($row->proof !== null)
          {
          	$proof = '<a class="popup-newWindow" href="'.Storage::disk('s3')->url($row->proof).'">Lihat</a>';
          }
          else
          {
          	$proof = '-';
          }

          // DATE CONFIRM
          if($row->date_confirm == null)
          {
            $date_confirm = '-';
          }
          else
          {
            $date_confirm = $row->date_confirm;
          }

          $data['data'][] = [
            0=>$row->no_order,
            1=>$row->package,
            2=>$api->pricing_format($row->price),
            3=>$api->pricing_format($row->total_price),
            4=>$proof,
            5=>$notes,
            6=>Carbon::parse($row->created_at)->toDateTimeString(),
            7=>$date_confirm,
            8=>$confirm
          ];
        }
      }
     
      echo json_encode($data);
    }

    /*
      ADMIN CONFIRM ORDER
    */
    public function confirm_order(Request $request)
    {
    	$order = Orders::find($request->id);

    	if(!is_null($order))
    	{
        $today = Carbon::now();
        $user = User::find($order->user_id);
    
        $order->date_confirm = $today;
    		$order->status = 2;
    		$order->save();

        $user = User::find($order->user_id);
        $check_active_membership = $this->check_term_membership($user);

        // dd($user->id);

        if($check_active_membership == 'active')
        {
          $data = [
            'user_id'=>$user->id,
            'order_id'=>$order->id,
            'order_package'=>$order->package
          ];

          return $this->orderLater($data);
        }
        else
        {
          $user->membership = $order->package;
          $user->end_membership = $today->addMonths(1);
          $user->status = 2;
          $user->save();
        }

    		$data['success'] = 1; 
    	}
    	else
    	{
    		$data['msg'] = Lang::get('order.id');
    		$data['success'] = 0;
    	}

    	return response()->json($data);
    }

    /*
      TO CHECK WHETTHER MEMBERSHIP STILL ACTIVE OR HAS END,
      IF ACTIVE, ORDER / PURCHASED ORDER WILL DELIVER TO TABLE MEMBERSHIP
    */
    private function check_term_membership($user)
    {
      if(!is_null($user))
      {
        $user_status = $user->status;
        if($user_status == 2)
        {
          return 'active';
        }
        else
        {
          return 'inactive';
        }
      }
    }

    /*
       DELIVER ORDER TO TABLE MEMBERSHIP
    */
    private function orderLater(array $data)
    {
      $check_membership = Membership::where('user_id',$data['user_id'])->orderBy('id','desc')->first();

      $membership = new Membership;
      $membership->user_id = $data['user_id'];
      $membership->order_id = $data['order_id'];

      if(is_null($check_membership))
      {
        $getDay = $this->updateLater($data['user_id']);
        $membership->start = $getDay['start'];
        $membership->end = $getDay['end'];
        $membership->status = 0;
      }
      else
      {
        //if available data
        $previous_end_day = Carbon::parse($check_membership->end)->setTime(0, 0, 0);
        $next_end_day = Carbon::parse($previous_end_day)->addMonths(1);
        
        $membership->start = $previous_end_day;
        $membership->end = $next_end_day;
      }

      try
      {
        $membership->save();
        $arr['success'] = 1;
      }
      catch(QueryException $e)
      {
        $arr['msg'] = Lang::get('order.err_membership');
        $arr['success'] = 0;
      }

      return response()->json($arr);
    }

    /*
      ORDER LATER
    */
    public function updateLater($user_id)
    {
        $user = User::find($user_id);
        $end_membership = $user->end_membership;
        $data['start'] = Carbon::parse($end_membership)->toDatetimeString();
        $data['end'] = Carbon::parse($data['start'])->setTime(0,0,0)->addMonths(1);
        return $data;
    }

    //CANCEL ORDER
    public function cancel_order(Request $request)
    {
    	$order = Orders::find($request->id);
    	if(!is_null($order))
    	{
    		$order->status = 3;
    		$order->save();
    		$data['success'] = 1; 
    	}
    	else
    	{
    		$data['msg'] = Lang::get('order.id');
    		$data['success'] = 0;
    	}

    	return response()->json($data);
    }

    // SET WA MESSAGE FOR ORDER
    public function set_order_message()
    {
        $notif = Notification::all()->first();
        return view('admin.order.message',['notif'=>$notif]);
    }

    public function save_message(Request $request)
    {
        $data = [
            'notif_order'=>$request->notif,
            'notif_after'=>$request->notif_order,
            'admin_id'=>$request->admin_id,
        ];

        $rules = [
            'notif'=>['required','max:65000'],
            'notif_order'=>['required','max:65000'],
            'admin_id'=>['required','numeric'],
        ];

        $valid = Validator::make($request->all(),$rules);

        if($valid->fails() == true)
        {
            $err = $valid->errors();
            $errors = [
                'status'=>'error',
                'notif'=>$err->first('notif'),
                'notif_order'=>$err->first('notif_order'),
                'admin_id'=>$err->first('admin_id')
            ];

            return response()->json($errors);
        }
        
        try
        {
           DB::table('notification')->update($data);
           $data['status'] = 1;
           $data['msg'] = Lang::get('custom.success');
        }
        catch(Queryexception $e)
        {
           $data['status'] = "error";
        }

        return response()->json($data);
    }

    public function LoginUser($id){
      Auth::loginUsingId($id, true);
      return redirect('account');
    }

/*end controller*/
}
