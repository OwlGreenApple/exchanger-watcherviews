<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController as adm;
use App\Models\User;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Event;
use App\Helpers\Price;
use App\Helpers\Api;
use App\Helpers\Messages;
use App\Mail\SellerEmail;
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
    	$src = $request->src;
    	$sort = $request->sort;
    	$range = $request->range;
    	$pc = new Price;

    	if($src == null)
    	{
    		$tr = Transaction::where([['transactions.status','=',0],['users.status','>',0],['users.status','<>',3]])->join('users','users.id','=','transactions.seller_id')->select('transactions.*','users.status','users.name');
    	}
    	else
    	{
    		$tr = Transaction::where([['transactions.status','=',0],['users.status','>',0],['users.status','<>',3]])->join('users','users.id','=','transactions.seller_id')
    			->where(function($query) use ($src) {
    				$query->where('transactions.total',$src);
    				$query->orWhere('transactions.amount',$src);
    				$query->orWhere('transactions.kurs',$src);
    				$query->orWhere('users.name',$src);
    			})->select('transactions.*','users.status','users.name');
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
    	if($tr->count() > 0)
    	{
    		$rate = ' ';
    		foreach($tr as $row):
    			// $seller = User::find($row->seller_id);
    			$cm = self::star_rate($row->seller_id);
    			$star = round($cm->star);
    			$data[] = [
    				'id'=>$row->id,
    				'no'=>$row->no,
    				'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($row->total),
    				'coin'=>$pc->pricing_format($row->amount),
    				'seller_name'=>$row->name,
    				'seller'=>$row->seller_id,
    				'kurs'=>$row->kurs,
    				'rate'=>$star,
    			];
    		endforeach;
    	}

    	return view('buyer.buy-table',['data'=>$data,'paginate'=>$tr]);
    }

    // REQUEST BUY TO SELLER
    public function buy_request(Request $request)
    {
        $tr = Transaction::where([['id',$request->id],['status',0]])->first();

        if(is_null($tr))
        {
            return response()->json(['err'=>1]);
        }

        $ts = Transaction::find($tr->id);
        $ts->buyer_id = Auth::id();
        $ts->date_buy = Carbon::now();
        $ts->status = 1;

        $seller = User::find($tr->seller_id);
        $msg = new Messages;
        $msg = $msg::seller_notification($tr->id);

        $notif = new Event;
        $notif->user_id = $seller->id;
        $notif->type = 1;
        $notif->event_name = 'Invoice : '.$tr->no;
        $notif->message = 'Selamat ada request order atas coin anda';
        $notif->url = 'sell';

        $url = '<a href="'.url('sell').'">Respon ke pembeli</a>';
        $data = [
          'message'=>$msg,
          'phone_number'=>$seller->phone_number,
          'email'=>$seller->email,
          'obj'=>new SellerEmail($tr->no,$url),
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
           $res['err'] = 0;
        }

        return response()->json($res);
    }

    public function detail_buy($id)
    {
    	$tr = Transaction::where([['id',$id],['status',0]])->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr) || $tr->seller_id == Auth::id())
    	{
    		return view('error404');
    	}

    	$user = User::find($tr->seller_id);
    	$pc = new Price;
        $cm = self::star_rate($tr->seller_id);

    	$data = [
    		'id'=>$tr->id,
    		'no'=>$tr->no,
            'star'=>round($cm->star),
    		'seller'=>$user->name,
    		'coin'=>$pc->pricing_format($tr->amount),
    		'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($tr->total),
    	];

        return view('buyer.buy-detail',['row'=>$data]);
    }

    public static function star_rate($seller_id)
    {
        $cm = Comment::selectRaw('AVG(rate) AS star')->where('seller_id',$seller_id)->first();
        return $cm;
    }

    public function deal($id)
    {
    	$tr = Transaction::find($id);
    	$pc = new Price;

    	if(is_null($tr) || $tr->status > 0)
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
    		'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($tr->total),
    	];

        return view('buyer.buy-deal',['row'=>$data,'user'=>$user]);
    }

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
    		$tr->date_buy = Carbon::now();
            $tr->buyer_id = Auth::id();
            $tr->payment = $payment_method;
    		$tr->status = 1;
    		$tr->save();
    		$data['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		$data['err'] = 1;
    	}

    	return response()->json($data);
    }

    public function buyer_history()
    {
    	$data = array();
    	$tr = Transaction::where('buyer_id',Auth::id())->orderBy('id','desc')->get();
    	$pc = new Price;

    	if($tr->count() > 0)
    	{
    		foreach($tr as $row)
    		{
    			if($row->status == 1)
    			{
    				$status = '<a target="_blank" href="'.url('buyer-confirm').'/'.$row->id.'" class="btn btn-primary btn-sm">Konfirmasi</a>';
    				$comments = '-';
    			}
				elseif($row->status == 2)
    			{
    				$status = '<span class="text-info">Tunggu seller</span>';
    				$comments = '-';
    			}
				elseif($row->status == 3)
    			{
    				$status = '<span class="text-black-50">Lunas</span>';
    				$comments = '<a href="'.url('comments').'/'.$row->no.'"><i class="far fa-envelope"></i></a>';
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
    			];
    		}

    		// dd($data);
    	}

        return view('buyer.buy-content',['data'=>$data]);
    }

    public function buyer_confirm($id)
    {
    	$tr = Transaction::where([['id',$id],['status',1]])->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr) || $tr->status > 1)
    	{
    		return view('error404');
    	}

        return view('buyer.buyer-confirm',['row'=>$tr]);
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

    public function buyer_proof(Request $request)
    {
    	$tr = Transaction::find($request->id);

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

    	try
    	{
    		$tr->upload = $proof;
    		$tr->date_buy = Carbon::now();
    		$tr->status = 2;
    		$tr->save();
    		$data['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		$data['err'] = 1;
    	}

    	return response()->json($data);
    }

    public function comments($invoice)
    {
    	$tr = Transaction::where('no',$invoice)->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr))
    	{
    		return view('error404');
    	}

        $seller = User::find($tr->seller_id);
        return view('buyer.comments',['tr'=>$tr,'seller'=>$seller]);
    }

    // SAVE COMMENT AND RATE FROM BUYER
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

    	$cm = new Comment;
    	$cm->buyer_id = Auth::id();
    	$cm->seller_id = $request->seller_id;
    	$cm->comments = $request->comments;
    	$cm->rate = $request->rate;
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
    	$com = Comment::where('seller_id',$request->seller_id)->get();

    	if($com->count() > 0)
    	{
    		foreach($com as $row)
    		{
    			$buyer = User::find($row->buyer_id);
    			$seller = User::find($row->seller_id);
    			$data[] = [
    				'buyer'=>$buyer->name,
    				'seller'=>$seller->name,
                    'no_trans'=>$row->no_trans,
    				'comments'=>$row->comments,
    				'rate'=>$row->rate,
    				'created_at'=>$row->created_at,
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

/*end class*/
}
