<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Price;
use App\Helpers\Api;
use Illuminate\Database\QueryException;
use App\Orders;
use Carbon\Carbon;
use Session, Auth, Storage, DB;

class OrderController extends Controller
{
    public function index()
    {
    	$page = request()->segment(2);
    	$page == null? $page = 1 : $page;
    	$part = [
    		'lang'=> new Lang,
    		'api'=> new Price,
    		'page'=>$page
    	];
    	return view('order.checkout',$part);
    }

    // SUBMIT PAYMENT
    public function submit_payment(Request $request)
    {
        $package = $request->package;
        $is_ajax = false;
        $price = new Price;
        $pack = $price->check_type($package);


        // in case if checkout from summary
        if(session('order') !== null)
        {
            $order = [
                "package" => session('order')['package'],
                "price" => session('order')['price'],
                "total" => session('order')['price'],
            ];
        }
        else
        {
            $order = [
                "package" => $package,
                "price" => $pack['price'],
                "total" => $pack['price'],
            ];
        }

        if(session('order') == null)
        {
          session(['order'=>$order]);
        }

        if($request->ajax() == true)
        {
            $is_ajax = true;
        }

        if(Auth::check() == false)
        {
          $rt['status'] = 1; // redirect to summary
          return response()->json($rt);
        }
        else
        {
          return $this->submit_order($order,$is_ajax);
        }
    }

    // SUMMARY PAGE IF USER NOT LOGGED IN
    public function summary()
    {
        if(session('order') == null)
        {
           return redirect('checkout');
        }

        $session = session('order');
        return view('order.summary',['session'=>$session,'lang'=>new Lang,'api'=>new Price]);
    }

    // WHEN USER HAS LOGED IN ALREADY
    public function submit_order(array $data,$is_ajax = false)
    {
        $dt = Carbon::now();
        $str = 'ACT'.$dt->format('ymd').'-'.$dt->format('Hi');
        
        $order = new Orders;
        $order_number = self::autoGenerateID($order, 'no_order', $str, 3, '0');
        $order->user_id = Auth::id();
        $order->no_order = $order_number;
        $order->package = $data['package'];
        $order->price = $data['price'];
        $order->total_price = $data['total'];

        try
        {
            $order->save();
            if(Auth::id() == null)
            {
                $rt['status'] = 1; //redirect to summary page
            }
            else
            {
                $rt['status'] = 2; //redirect to thankyou page

                // SEND WA MESSAGE IF ORDER SUCCESSFUL  
                /*$this->send_message($data['package'],$link,$data['price'],$data['total'],$data['purchased_views'],$order_number,Auth::user()->phone_number);*/
            }

            if(session('order') !== null)
            {
                Session::forget('order');
                
            }
        }
        catch(QueryException $e)
        {
             // dd($e->getMessage());
            $rt['status'] = 0;
            $rt['msg'] = $this->lang::get('custom.failed');
        }

        // in case of ajax
        if($is_ajax == true)
        {
            return response()->json($rt);
        }
        else
        {
            if($rt['status'] = 2)
            {
                return redirect('thankyou');
            }
            else
            {
                return redirect('summary')->with('status', Lang::get('custom.failed'));
            }
        }
    }

    // GENERATE ID FOR ORDER NUMBER
    public static function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
    {
        $tb = $model->select(DB::raw("substr(".$field.", ".strval(strlen($search)+1).") as lastnum"))->whereRaw("substr(".$field.", 1, ".strlen($search).") = '".$search."'")->orderBy('id', 'DESC')->first();
                                        
        if ($tb == null){
            $ctr = 1;
        }
        else{
            $ctr = intval($tb->lastnum) + 1;
        }
        return $search.'-'.str_pad($ctr, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    // SEND MESSAGE TO USER'S WA
    public function send_message($package,$link,$price,$total,$purchase,$order_number,$phone_number,$after = null)
    {
        $api = new Api;
        $notif = Notification::all()->first();
        $admin_id = $notif->admin_id;

        // after = if NOT null message is for 6 hours after order
        if($after == null)
        {
            $txt = $notif->notif_order;
        }
        else
        {
            $txt = $notif->notif_after;
        }
       
        $message = $this->replace_string_order($package,$link,$api->pricing_format($price),$api->pricing_format($total),$api->pricing_format($purchase),$order_number,$txt);

        $api->send_wa_message($admin_id,$message,$phone_number);
    }

    // REPLACE SPECIAL CHARACTER ACCORDING ON ORDER
    public function replace_string_order($package,$link,$price,$total,$purchase,$order_number,$message)
    {
        $replace_target = array(
          '[PACKAGE]','[PRICE]','[TOTAL]','[PURCHASE]','[NO-ORDER]'
        );

        $replace = array(
          $package,$price,$total,$purchase,$order_number
        );

        return str_replace($replace_target,$replace,$message);
    }

    public function thankyou()
    {
        self::no_auth();
        return view('order.thankyou');
    }

    private static function no_auth()
    {
        if(Auth::check() == false)
        {
            return redirect('checkout');
        }
    }

   // ORDER PAGE
    public function order()
    {
        return view('order.index',['lang'=>$this->lang]);
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
        if(preg_match("/^https\:\/\/(www)\.(youtube)\.(com)\/watch\?v\=.+/i", $src))
        {
           $order = Orders::where('link','=',$src);
        }
        elseif(preg_match("/@/i", $src))
        {
           $order = Orders::where('link','LIKE','%'.$src.'%');
        }
        elseif(preg_match("/^[a-zA-Z ]*$/i",$src) == 1)
        {
           $order = Orders::where('package','LIKE','%'.$src.'%');
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $order = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        elseif(preg_match("/^[0-9]*$/i",$src))
        {
          $order = Orders::where('purchased_views','=',$src);
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
      $api = new Api;

      if($orders->count())
      {
        $no = 1;
        $extract = new CheckYtLink;

        foreach($orders as $order)
        {
          
          if($order->status == 3)
          {
            $date_complete = Carbon::parse($order->updated_at)->toDateTimeString();
          }
          else
          {
            $date_complete = '-';
          }

          if(($order->proof !== null && $order->status > 1) || ($order->proof == null && $order->status > 1))
          {
            $proof = '-';
          }
          elseif($order->proof == null)
          {
            $proof = '<button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#confirm-payment" data-id="'.$order->id.'" data-no-order="'.$order->no_order.'" data-package="'.$order->package.'" data-total="'.$order->total_price.'" data-date="'.$order->created_at.'" data-purchased-view="'.$order->purchased_views.'" style="font-size: 13px; padding: 5px 8px;">'.Lang::get('order.confirm').'
              </button>';
          }
          else
          {
            $proof = '<a class="popup-newWindow" href="'.Storage::disk('s3')->url($order->proof).'">View</a>';
          }

          if($order->status==1)
          {
            $status = '<span style="color: blue"><b>'.Lang::get('order.process').'</b></span>';
          }
          elseif($order->status==2)
          {
            $status = '<span style="color: orange"><b>'.Lang::get('order.partial').'</b></span>';
          }
          elseif($order->status==3)
          {
            $progress = new CheckYtLinkProgress;
            $progress = $progress->get_complete_date($order->updated_at);
            $repromote = '';

            if($progress == true)
            {
                $repromote = '<div class="mt-1"><a class="text-primary repromote" id="'.$order->id.'" data-id="'.$order->id.'" data-package="'.$order->package.'" data-price="'.$order->price.'" data-total="'.$order->total_price.'" data-date="'.$order->created_at.'" data-purchased-view="'.$order->purchased_views.'" data-text="'.Lang::get("order.re-promote").'">Re-Promote</a></div>';
            }
            else
            {
                // refill
                $repromote = '<div class="mt-1"><a class="text-primary refill" id="'.$order->id.'">Refill</a></div>';
            }

            $status = '<div><span class="badge badge-success px-2 py-2">'.Lang::get('order.complete').'</span></div>'.$repromote.'';
          }
          elseif($order->status==4)
          {
                $status = '<div class="mt-1"><button type="button" class="btn btn-primary btn-sm repromote" id="'.$order->id.'" data-package="'.$order->package.'" data-price="'.$order->price.'" data-total="'.$order->total_price.'" data-date="'.$order->created_at.'" data-purchased-view="'.$order->purchased_views.'" data-text="'.Lang::get("order.re-promote").'">Re-Promote</button></div>';
          }
          elseif($order->status==6)
          {
            $status = '<span style="color: red"><b>'.Lang::get('order.cancel').'</b></span>';
          }
          elseif($order->status==7)
          {
            $status = '<div><span class="badge badge-success px-2 py-2">'.Lang::get('order.complete').'</span></div>';
          }
          else
          {
            $status = '<b>'.Lang::get('order.waiting').'</b>';
          }

          // THUMBNAIL
          if($order->package_type==1)
          {
            $thumbnail = '<img width="80" height="45" src="https://img.youtube.com/vi/'.$extract->extract_youtube_value($order->link).'/0.jpg" />';
          }
          else
          {
            $thumbnail = '<div align="center">-</div>';
          }
          
          $data['data'][] = [
            0=>$order->no_order,
            1=>$order->package,
            2=>$thumbnail,
            3=>'<div style="word-break: break-all">'.$order->link.'</div>',
            4=>Lang::get('custom.currency')." ".$api->pricing_format($order->price),
            5=>Lang::get('custom.currency')." ".$api->pricing_format($order->total_price),
            6=>$api->pricing_format($order->purchased_views),
            7=>$api->pricing_format($order->start_view),
            8=>$api->pricing_format($order->views),
            9=>Carbon::parse($order->created_at)->toDateTimeString(),
            10=>$date_complete,
            11=>$proof,
            12=>$status
          ];
        }
      }
     
      echo json_encode($data);
                
    }

    //upload bukti TT 
      public function confirm_payment_order(Request $request){
        $user = Auth::user();
        //konfirmasi pembayaran user
        $order = Orders::find($request->id_confirm);
        $folder = $user->email.'/buktibayar';
        $celeb = null;
        $pathUrl = str_replace(url('/'), '', url()->previous());

        if(strlen($request->keterangan) > 300)
        {
            return redirect($pathUrl)->with("error", $this->lang::get('order.max_notes'));
        }

        if(env('APP_ENV') == 'local')
        {
            $celeb = 'celebfans';
        }

        if($order->status==0)
        {
          $order->status = 1;

          if($request->hasFile('buktibayar'))
          {
            // $path = Storage::putFile('bukti',$request->file('buktibayar'));
            $dir = $celeb.'/bukti_bayar/'.explode(' ',trim($user->name))[0].'-'.$user->id;
            $filename = $order->no_order.'.jpg';
            Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('buktibayar')), 'public');
            $order->proof = $dir."/".$filename;
            
          } else {
            return redirect($pathUrl)->with("error", $this->lang::get('order.upload_first'));
          }  
          $order->notes = $request->keterangan;
          $order->save();
        } 
        else 
        {
            return redirect($pathUrl)->with("error", $this->lang::get('order.reject'));
        }

        return view('order.thankyou-confirm-payment',['lang'=>$this->lang]);
      }

/*end class*/
}
