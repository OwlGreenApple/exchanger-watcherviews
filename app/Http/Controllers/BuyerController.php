<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController as adm;
use App\Http\Controllers\HomeController as Home;
use App\Models\User;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Event;
use App\Helpers\Price;
use App\Helpers\Api;
use App\Helpers\Messages;
use App\Mail\SellerEmail;
use App\Mail\BuyerEmail;
use App\Mail\EmergencyEmail;
use Carbon\Carbon;
use Storage, Session, Validator;

class BuyerController extends Controller
{
    public function buying_page()
    {
        return view('buyer.buy',['pc'=>new Price]);
    }

    public function buyer_table(Request $request)
    {
    	$paginate = 10;
    	$src = strip_tags($request->src);
    	$sort = strip_tags($request->sort);
    	$range = strip_tags($request->range);
    	$pc = new Price;

        $logic = Transaction::where([['transactions.status','=',0],['users.status','>',0],['users.status','<>',3]])->join('users','users.id','=','transactions.seller_id')->select('transactions.*','users.status','users.name','users.blocked_buyer');

    	if($src == null)
    	{
    		$tr = $logic;
    	}
    	else
    	{
    		$tr = Transaction::where([['transactions.status','=',0],['users.status','>',0],['users.status','<>',3]])->join('users','users.id','=','transactions.seller_id')
    			->where(function($query) use ($src) {
    				$query->orWhere('transactions.amount',$src);
    				$query->orWhere('users.name',$src);
    			})->select('transactions.*','users.status','users.name','users.blocked_buyer');
    	}

    	// RANGE LOGIC
    	if($range == '1')
    	{
    		$order = 'desc';
    	}
    	else
    	{
    		$order = 'asc';
    	}

    	// SORTING LOGIC
    	if($sort == 'coin')
    	{
    		$tr = $tr->orderBy('transactions.amount',$order)->paginate($paginate);
    	}
    	elseif($sort == 'rate')
    	{
    		$tr = $tr->orderBy('transactions.kurs',$order)->paginate($paginate);
    	}
    	elseif($sort == 'price')
    	{
    		$tr = $tr->orderBy('transactions.total',$order)->paginate($paginate);
    	}
    	else
    	{
    		$tr = $tr->orderBy('transactions.id',$order)->paginate($paginate);
    	}

    	$data = array();
        $find = false;

    	if($tr->count() > 0)
    	{
    		$data = self::seller_result($tr,$pc);
            $find = null;
    	}
        else
        {
            $tr = $logic;
            $tr = $tr->orderBy('transactions.id',$order)->paginate($paginate);
            $data = self::seller_result($tr,$pc);
            $find = $src;
        }

    	return view('buyer.buy-table',['data'=>$data,'find'=>$find,'paginate'=>$tr]);
    }

    // LOGIC ON ABOVE
    private static function seller_result($tr,$pc)
    {
        $rate = ' ';
        $kurs = $pc::get_rate();
        $data = array();
        
        foreach($tr as $row):
            if($pc::check_blocked_user($row->blocked_buyer) == true)
            {
                continue;
            }

            $cm = self::star_rate($row->seller_id);
            $star = $cm['star'];
            $star_float = $cm['star_float'];
            
            $total_price = $kurs * $row->total;
            $data[] = [
                'id'=>$row->id,
                'no'=>$row->no,
                'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($total_price),
                'coin'=>$pc->pricing_format($row->amount),
                'seller_name'=>$row->name,
                'seller'=>$row->seller_id,
                'rate'=>$star,
                'star_float'=>$star_float,
                'link'=>''.url('comments').'/'.encrypt($row->seller_id).'',
            ];
        endforeach;

        return $data;
    }

    // REQUEST BUY TO SELLER
    public function buy_request(Request $request)
    {
        $pc = new Price;
        $tr = Transaction::where([['id',$request->id],['status',0]])->first();

        if(is_null($tr))
        {
            return view('error404');
        }

        $ts = Transaction::find($tr->id);
        $ts->buyer_id = Auth::id();
        $ts->date_buy = Carbon::now();
        $ts->kurs = $pc::get_rate();
        $ts->total = round($pc::get_rate() * $ts->total);
        $ts->status = 1;

        $seller = User::find($tr->seller_id);
        $msg = new Messages;
        $msg = $msg::seller_notification($ts->no,null,$pc->pricing_format($ts->amount),$pc->pricing_format($ts->total),$ts->id);

        $notif = new Event;
        $notif->user_id = $seller->id;
        $notif->type = 1;
        $notif->event_name = 'Invoice : '.$ts->no;
        $notif->message = 'Selamat ada request order atas coin anda';
        $notif->url = 'sell-detail/'.$ts->id;

        $url = '<a href="'.url('sell-detail').'/'.$ts->id.'">Respon ke pembeli</a>';
        $data = [
          'message'=>$msg,
          'phone_number'=>$seller->phone_number,
          'email'=>$seller->email,
          'obj'=>new SellerEmail($tr->no,$url,null,$pc->pricing_format($ts->amount),$pc->pricing_format($ts->total)),
        ]; 

        try
        {
           $notif->save();
           $ts->save();
           $adm = new adm;
           $adm->notify_user($data);
           $res['err'] = 0;
        }
        catch(QueryException $e)
        {
            //dd($e->getMessage())
           $res['err'] = 1;
        }

        return response()->json($res);
    }

    public function request_buy_message()
    {
        return view('buy-request');
    }

    //NOTIFICATION FOR BUYER WHEN SELLER ACCEPT REQUEST ORDER
    public static function notify_buyer($invoice=null,$buyer_id=0,$trans_id=0,$coin=0,$total=0)
    {
        $url = '<a href="'.url('deal').'/'.$trans_id.'">Bayar Order</a>';
        $msg = new Messages;
        $msg = $msg::buyer_notification($invoice,$trans_id,$coin,$total);

        $buyer = User::find($buyer_id);
        $data = [
          'message'=>$msg,
          'phone_number'=>$buyer->phone_number,
          'email'=>$buyer->email,
          'obj'=>new BuyerEmail($invoice,$url,$coin,$total),
        ];

        $adm = new adm;
        $adm->notify_user($data);
    }

    public function detail_buy($id)
    {
    	$tr = Transaction::where([['id',$id],['status',0]])->first();
    	// TO AVOID IF USER DELIBERATELY PUT THIS PRODUCT FROM URL
    	if(is_null($tr) || $tr->seller_id == Auth::id())
    	{
    		return view('error404');
    	}

    	$user = User::find($tr->seller_id);
        $pc = new Price;
        $kurs = $pc::get_rate();

         // FOR BLOCKED BUYER BY SELLER
        if($user->blocked_buyer !== null)
        {
            if($pc::check_blocked_user($user->blocked_buyer) == true)
            {
                return view('error404');
            }
        }

        $cm = self::star_rate($tr->seller_id);

        $paymethod = '';
        if($user->bank_1 !== null || $user->bank_2 !== null)
        {
            $paymethod .= 'Transfer Bank';
        }

        ($user->epayment_1 !== null)?$paymethod.=" ,".$pc::explode_payment($user->epayment_1)[0] : $paymethod.= null;

        ($user->epayment_2 !== null)?$paymethod.=" ,".$pc::explode_payment($user->epayment_2)[0] : $paymethod.= null;

        ($user->epayment_3 !== null)?$paymethod.=" ,".$pc::explode_payment($user->epayment_3)[0] : $paymethod.= null;
        
        $total = $tr->total * $kurs;
    	$data = [
    		'id'=>$tr->id,
    		'no'=>$tr->no,
            'rate'=>$cm['star'],
            'star_float'=>$cm['star_float'],
    		'seller'=>$user->name,
    		'coin'=>$pc->pricing_format($tr->amount),
            'paymethod'=>$paymethod,
    		'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($total),
    	];

        return view('buyer.buy-detail',['row'=>$data]);
    }

    public static function star_rate($seller_id)
    {
        $cm = Comment::selectRaw('AVG(rate) AS star')->where([['seller_id',$seller_id],['is_seller',0]])->first();

        
        $data = self::float_fix($cm->star);
        return $data;
    }

    public static function float_fix($rate)
    {
        $star_float = 0;
        $star = number_format((int)$rate, 1, '.', '');

        if(is_float($rate) == true)
        {
            $star_float = number_format((float)$rate, 1, '.', '');
            $star_float = $star_float - $star;
        }

        $data = [
            'star'=>$star,
            'star_float'=>$star_float,
        ];

        return $data;
    }

    public function deal($id)
    {
    	$tr = Transaction::find($id);
    	$pc = new Price;
        $hm = new Home;

    	if(is_null($tr) || $tr->status <> 2 || $tr->buyer_id !== Auth::id() || $tr->seller_id == Auth::id())
    	{
    		return view('error404');
    	}

    	//seller stuff
    	$seller_id = $tr->seller_id;
    	$user = User::find($seller_id);
        $epay_1 = $epay_2 = $epay_3 = null;

        if($user->epayment_1 !== null)
        {
            $epay_1 = Storage::disk('s3')->url($pc::explode_payment($user->epayment_1)[1]);
        }

        if($user->epayment_2 !== null)
        {
            $epay_2 = Storage::disk('s3')->url($pc::explode_payment($user->epayment_2)[1]);
        }

        if($user->epayment_3 !== null)
        {
            $epay_3 = Storage::disk('s3')->url($pc::explode_payment($user->epayment_3)[1]);
        }

    	$data = [
    		'id'=>$tr->id,
    		'no'=>$tr->no,
    		'seller'=>$user->name,
            'epay_1'=>$epay_1,
            'epay_2'=>$epay_2,
            'epay_3'=>$epay_3,
    		'coin'=>$pc->pricing_format($tr->amount),
            'total_payment'=>$hm::check_many_payments($seller_id),
    		'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($tr->total),
    	];

        return view('buyer.buy-deal',['row'=>$data,'user'=>$user]);
    }

    public function buyer_history()
    {
    	$data = array();
    	$tr = Transaction::where('buyer_id',Auth::id())->orderBy('updated_at','desc')->get();
    	$pc = new Price;
        $action = "-";

    	if($tr->count() > 0)
    	{
    		foreach($tr as $row)
    		{
                $comments_btn = '<a href="'.url('comments').'/'.encrypt($row->seller_id).'/'.$row->no.'"><i class="far fa-envelope"></i></a>';

    			if($row->status == 1)
    			{
    				$status = '<span class="text-info">Tunggu seller</span>';
    				$comments = '-';
    			}
				elseif($row->status == 2)
    			{
                    $status = '<span class="text-info">Lakukan Pembayaran</span>';
                    $action = '<a target="_blank" href="'.url('deal').'/'.$row->id.'" class="btn btn-success btn-sm">Bayar Sekarang</a>';
    				$comments = '-';
    			}
				elseif($row->status == 3)
    			{
    				$status = '<span class="text-black-50">Lunas</span>';
    				$comments = $comments_btn;
    			}
                elseif($row->status == 4)
                {
                    if($row->buyer_dispute_id > 0)
                    {
                        /*$status = '<a target="_blank" href="'.url('chat').'/'.$row->id.'" class="btn btn-outline-primary btn-sm"><i class="far fa-comments"></i>&nbsp;Chat Admin</a>';*/
                        $status = '<span class="badge badge-secondary">Dispute sedang diproses</span>';
                    }
                    else
                    {
                        $status = ' <a target="_blank" href="'.url('buyer-dispute').'/'.$row->id.'" class="btn btn-danger btn-sm">Dispute</a>';
                    }      
                    $comments = '-';
                } 
                elseif($row->status == 5)
                {
                    $status = '<span class="text-black-50">Dispute - penjual menang</span>';
                    $comments = $comments_btn;
                }
                elseif($row->status == 6)
                {
                    $status = '<span class="text-black-50">Dispute Admin</span>';
                    $comments = $comments_btn;
                }
                elseif($row->status == 7)
                {
                    $status = '<span class="text-primary">Tunggu konfirmasi seller</span>';
                    $comments = '-';
                }
    			else
    			{
    				$comments = $status = '-';
    			}

    			$seller = User::find($row->seller_id);
    			$data[] = [
    				'id'=>$row->id,
    				'seller'=>$seller->name,
    				'date'=>$row->date_buy,
    				'no'=>$row->no,
    				'coin'=>$pc->pricing_format($row->amount),
    				'kurs'=>$row->kurs,
    				'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($row->total),
    				'payment'=>$row->payment,
                    'comments'=>$comments,
    				'status'=>$status,
                    'action'=>$action,
    			];
    		}

    		// dd($data);
    	}

        return view('buyer.buy-content',['data'=>$data]);
    }

    public function buyer_confirm($id)
    {
        $pc = new Price;
    	$tr = Transaction::where([['id',$id],['status',2]])->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr) || $tr->status <> 2 || $tr->buyer_id !== Auth::id() || $tr->seller_id == Auth::id())
    	{
    		return view('error404');
    	}

        return view('buyer.buyer-confirm',['row'=>$tr,'pc'=>$pc]);
    }

    // UPDATE TRANSACTION PAYMENT METHOD
    public function buyer_deal(Request $request)
    {
        $id = $request->id;
        $payment_method = $request->payment_method;
        $tr = Transaction::find($id);

        if(is_null($tr))
        {
            return response()->json(['err'=>1]);
        }

        try
        {
            $tr->payment = $payment_method;
            $tr->save();
            $data['err'] = 0;
        }
        catch(QueryException $e)
        {
            $data['err'] = 1;
        }

        return response()->json($data);
    }

    public function buyer_proof(Request $request)
    {
    	$tr = Transaction::find($request->id);
        $pc = new Price;

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr))
    	{
    		return response()->json(['err'=>1]);
    	}

    	$user = Auth::user();
    	if($request->hasFile('bukti'))
	    {
	        $dir = env('S3path').'/bukti_bayar/'.explode(' ',trim($user->name))[0].'-'.$user->id;
	        $filename = $tr->no.'.jpg';
	        Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('bukti')), 'public');
	        $proof = $dir."/".$filename;
	        
	    } else {
	        return response()->json(['err'=>2]);
	    }  

        $seller_id = $tr->seller_id;
        $user = User::find($seller_id);

         /* notification to seller */
        $adm = new adm;
        $msg = new Messages;
        $coin = $pc->pricing_format($tr->amount);
        $total_money = $pc->pricing_format($tr->total);
        $msg = $msg::seller_notification($tr->no,$tr->id,$coin,$total_money);

        $notif = new Event;
        $notif->user_id = $user->id;
        $notif->type = 1;
        $notif->event_name = 'Invoice : '.$tr->no;
        $notif->message = 'Pembeli telah upload bukti bayar';
        $notif->url = 'sell-confirm/'.$tr->id;

        $url = '<a href="'.url('sell-confirm').'/'.$tr->id.'">Konfirmasi Penjualan</a>';
        $data = [
          'message'=>$msg,
          'phone_number'=>$user->phone_number,
          'email'=>$user->email,
          'obj'=>new SellerEmail($tr->no,$url,$tr->id,$coin,$total_money),
        ]; 

    	try
    	{
    		$tr->upload = $proof;
    		$tr->status = 7;
    		$tr->save();
            $notif->save();
            $adm->notify_user($data);
    		$data['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		$data['err'] = 1;
    	}

    	return response()->json($data);
    }

    public function comments($user_id,$invoice = null)
    {
    	$tr = null;
        if($invoice !== null)
        {
            $tr = Transaction::where('no',$invoice)->first();
            // TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
            if(is_null($tr))
            {
                return view('error404');
            }
        }
        
        $seller = User::find(decrypt($user_id));
        if(is_null($seller))
        {
            return view('error404');
        }

        return view('buyer.comments',['tr'=>$tr,'user_id'=>$seller->id,'member'=>$seller,'role'=>0,'invoice'=>$invoice]);
    }

    // SAVE COMMENT AND RATE FROM BUYER / SELLER
    public function save_comments(Request $request)
    {
    	$rule = [
    		'comments'=>['max : 250']
    	];

    	$validator = Validator::make($request->all(),$rule);
    	if($validator->fails())
    	{
    		$err = $validator->errors();
    		$error = [
    			'err'=>'validation',
    			'comments'=>$err->first('comments'),
    		];

    		return response()->json($error);
    	}

        // TO DETERMINE WHO MAKES COMMENT
        if($request->role == 1)
        {
            $buyer_id =  $request->user_id;
            $seller_id = Auth::id();
            $role = 1;
        }
        else
        {
            $buyer_id =  Auth::id();
            $seller_id = $request->user_id;
            $role = 0;
        }

    	$cm = new Comment;
    	$cm->buyer_id = $buyer_id;
    	$cm->seller_id = $seller_id;
    	$cm->comments = strip_tags($request->comments);
    	$cm->rate = strip_tags($request->rate);
        $cm->is_seller = $role;
        $cm->no_trans = $request->no_trans;

    	try
    	{
    		$cm->save();
    		$data['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		$data['err'] = 1;
    	}

    	return response()->json($data);
    }

    // DISPLAY COMMENTS VIA AJAX
    public function display_comments(Request $request)
    {
    	$data = array();
        $role = $request->role;

        if($role == 1)
        {
            $user_role = 'buyer_id';
        }
        else
        {
            $user_role = 'seller_id';
        }

    	$com = Comment::where([[$user_role,$request->user_id],['is_seller',$role]])->orderBy('id','desc')->get();

    	if($com->count() > 0)
    	{
    		foreach($com as $row)
    		{
                if($role == 1)
                {
                    $user = User::find($row->seller_id);
                }
                else
                {
                    $user = User::find($row->buyer_id);
                }

                $rate = self::float_fix($row->rate);
    			$data[] = [
    				'user'=>$user->name,
            'no_trans'=>$row->no_trans,
    				'comments'=>$row->comments,
    				'rate'=>$rate['star'],
            'star_float'=>$rate['star_float'],
    				'created_at'=>$row->created_at,
            'role'=>$role,
    			];
    		}

    		return view('buyer.comments-data',['data'=>$data]);
    	}
    }

    //DISPUTE
    public function buyer_dispute($id)
    {
        $tr = Transaction::where([['id',$id],['buyer_id',Auth::id()]])->first();

        if(is_null($tr))
        {
            return view('error404');
        }

        return view('buyer.buyer-dispute',['tr'=>$tr]);
    }

    public function test_wa()
    {
        $tr = 'SSS';
        $api = new Api;

        $msg ='';
        $msg .='Selamat coin anda dengan no invoice *SSS*'."\n";
        $msg .='telah di order'."\n";
        $msg .='Silahkan login dan lakukan konfimasi di sini'."\n\n";
        $msg .=url('sell-confirm').'/'.$tr."\n\n";
        $msg .='Terima Kasih'."\n";
        $msg .='Team Exchanger';
        $api->send_wa_message(15,$msg,'62895342472008');
    }

/*end class*/
}
