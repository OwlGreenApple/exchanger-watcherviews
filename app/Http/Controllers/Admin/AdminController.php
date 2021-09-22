<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController as Odc;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\User;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Dispute;
use App\Models\Transaction;
use App\Models\Kurs;
use App\Helpers\Api;
use App\Helpers\Price;
use App\Mail\NotifyEmail;
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
            ->select('dp.upload_identity',
              'dp.upload_proof AS buyer_proof',
              'dp.upload_mutation',
              'dp.user_id AS buyer_id',
              'dp.created_at AS buyer_dispute_date',
              'ds.upload_proof AS seller_proof',
              'ds.created_at AS seller_dispute_date',
              'ds.user_id AS seller_id',
              'us.name AS buyer_name',
              'ur.name AS seller_name',
              'transactions.no AS invoice','transactions.date_buy','transactions.id')
            ->where([['transactions.status','=',4],['transactions.seller_dispute_id','>',0]])->orWhere('transactions.buyer_dispute_id','>',0)->orderBy('transactions.id','desc')->get();

      // dd($dp);

       return view('admin.dispute.content',['data'=>$dp]);
    }

    // PENDING
    public function notify_user(Request $request)
    {
      $user_id = $request->user_id;
      $invoice = $request->invoice;
      $odc = new Odc;

      $user = User::find($user_id);
      if(is_null($user))
      {
        return response()->json(['err'=>1]);
      }
      
      Mail::to($user->email)->send(new NotifyEmail($generated_password,$data['username']));

       $odc->send_message($data['package'],$data['price'],$data['total'],$order_number,Auth::user()->phone_number);
    }

    public function dispute_user(Request $request)
    {
      $dp = Dispute::where('disputes.id',$request->id)->join('users','users.id','=','disputes.user_id')->join('transactions AS tr','tr.id','=','disputes.trans_id')->select('disputes.*','tr.status AS trs','tr.seller_id','tr.buyer_id','users.warning')->first();

      if(is_null($dp))
      {
        return response()->json(['err'=>2]);
      }

      $tr = Transaction::find($dp->trans_id);
      $coin = $tr->amount;

      // BUYER DISPUTE
      if($dp->buyer_id == $dp->user_id)
      {
        // SELLER GET WARNING
        $user = User::find($dp->seller_id);
        $user->warning++;
        $total_warning = $user->warning;

        // BANNED SELLER IF WARNING HAS REACH 3
        if($total_warning > 2)
        {
          $user->status = 0;
        }

        try
        {
          $user->coin += $coin;
          $user->save();
        }
        catch(QueryException $e)
        {
          return response()->json(['err'=>1,['msg'=>$e->getMessage()]]);
        }
      }

      // CHANGE TRANSACTION STATUS IF ADMIN HAS DECIDE
      try
      {
        $tr->status = 3;
        $tr->save();
      }
      catch(QueryException $e)
      {
         return response()->json(['err'=>1,['msg'=>$e->getMessage()]]);
      }

      // CHANGE DISPUTE STATUS IF ADMIN HAS DECIDE
      try
      {
        $dispute = Dispute::find($dp->id);
        $dispute->status = 1;
        $dispute->save();
        $ret['err'] = 0;
      }
      catch(QueryException $e)
      {
        $ret['err'] = 1;
        $ret['msg'] = $e->getMessage();
      }

      return response()->json($ret);

      // SELLER CASE
     /* if($dp->seller_id == $dp_user_id)
      {
        $user = 
      }*/

    }

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


/*end controller*/
}
