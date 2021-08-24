<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\User;
use App\Models\Membership;
use App\Helpers\Api;
use App\Helpers\Price;
use Carbon\Carbon;
use Storage;

class AdminController extends Controller
{
    public function index()
    {
    	return view('admin.order.index');
    }

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
          else
          {
          	$confirm = '<b class="text-success">Terkonfirmasi</b>';
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
            $date_confirm =Carbon::parse($row->date_confirm)->setTimezone('Asia/Jakarta')->toDateTimeString();
          }

          $data['data'][] = [
            0=>$row->no_order,
            1=>$row->package,
            2=>$api->pricing_format($row->price),
            3=>$api->pricing_format($row->total_price),
            4=>$proof,
            5=>$notes,
            6=>Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta')->toDateTimeString(),
            7=>$date_confirm,
            8=>$confirm
          ];
        }
      }
     
      echo json_encode($data);
    }

    public function confirm_order(Request $request)
    {
    	$order = Orders::find($request->id);

    	if(!is_null($order))
    	{
        $today = Carbon::now()->setTimezone('Asia/Jakarta');
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

    //TO CHECK WHETHER MEMBERSHIP STILL ACTIVE
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

    public function updateLater($user_id)
    {
        $user = User::find($user_id);
        $end_membership = $user->end_membership;
        $data['start'] = Carbon::parse($end_membership)->setTimezone('Asia/Jakarta');
        $data['end'] = Carbon::parse($data['start'])->setTime(0,0,0)->addMonths(1);
        return $data;
    }

    //CANCEL ORDER
    public function cancel_order(Request $request)
    {
    	$order = Orders::find($request->id);
    	if(!is_null($order))
    	{
    		$order->status = 6;
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

/*end controller*/
}
