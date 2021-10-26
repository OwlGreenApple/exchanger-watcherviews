<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BuyerController AS Buyer;
use App\Http\Controllers\Admin\AdminController AS adm;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Comment;
use App\Helpers\Messages;
use App\Helpers\Price;
use App\Helpers\Api;
use App\Mail\BuyerEmail;
use Carbon\Carbon;
use Storage, Session;

class SellerController extends Controller
{
    public function selling_page()
    {
        $pc = new Price;
        $fee = $pc->check_type(Auth::user()->membership)['fee'];
        return view('seller.sell',['lang'=>new Lang,'pc'=>new Price,'fee'=>$fee]);
    }

    public function selling_save(Request $request)
    {
        $pc = new Price;
    	$coin = $pc::convert_number($request->tr_coin);
        $fee = $pc->check_type(Auth::user()->membership)['fee'];
        
        $coin_fee = ($coin * $fee)/100;
        $total_coin = $coin + $coin_fee;
        $total_price = $coin;
        $seller_name = Auth::user()->name;
        $invoice_name = self::get_name($seller_name);

        $dt = Carbon::now();
        $str = $invoice_name.$dt->format('ymd');

        $tr = new Transaction;
        $order_number = self::get_order_invoice($str);
        $tr->seller_id = Auth::id();
        $tr->no = $order_number;
        $tr->coin_fee = $coin_fee;
        $tr->amount = $coin;
        $tr->total = $total_price;

        $user = User::find(Auth::id());
        $coin = $user->coin;
        $user->coin -= $total_coin;

        if($user->membership == 'free')
        {
            $tr->trial = 1;
        	$user->trial--;
        }

        try
        {
        	$user->save();
        	$tr->save();
        	$data['err'] = 0;
        	$data['wallet'] = $user->coin;
        }
        catch(QueryException $e)
        {
        	//$e->getMessage()
        	$data['err'] = Lang::get('custom.failed');
        }

        return response()->json($data);
    }

    // DISPLAY TRANSACTION
    public function display_sell(Request $request)
    {
    	$data = array();
        $price = new Price;
        $kurs = $price::get_rate();
    	$tr = Transaction::where('transactions.seller_id','=',Auth::id())->leftJoin('users','transactions.buyer_id','=','users.id')->select('transactions.*','users.name as buyer')->orderBy('transactions.updated_at','desc')->get();


    	if($tr->count() > 0)
    	{
            $rate = '-';
    		foreach($tr as $row):
                $total_price = $row->total;
                $confirm_sell = '<a target="_blank" href="'.url("sell-confirm").'/'.$row->id.'" class="btn btn-success btn-sm">Konfirmasi</a>';
                
    			if($row->status == 0)
	    		{
                    // SUSPENDED USER
                    if(Auth::user()->status == 3)
                    {
                        $status = '<span class="text-danger">Suspend</span>';
                    }
                    else
                    {
                        $status = '<a data-id="'.$row->id.'" class="text-danger del_sell"><i class="fas fa-trash-alt"></i></a>';
                    }
                    $total_price = round($kurs * $row->total);
	    		}
	    		elseif($row->status == 1)
	    		{
	    			$status = '<a href="'.url("sell-detail").'/'.$row->id.'" class="btn btn-info">Lihat Request</a>';
	    		}
                elseif($row->status == 2)
                {
                    $status = '<span class="text-black">Tunggu konfirmasi pembeli</span>';
                }
                elseif($row->status == 4)
                {
                    $rate = '-';
                    if($row->seller_dispute_id > 0)
                    {
                        /*$status = '<a target="_blank" href="'.url('chat').'/'.$row->id.'" class="btn btn-outline-primary btn-sm"><i class="far fa-comments"></i>&nbsp;Chat Admin</a>';*/
                        $status = '<span class="badge badge-secondary">Dispute sedang diproses</span>';
                    }
                    else
                    {
                        $status = $confirm_sell;
                    }      
                }
	    		elseif($row->status == 5)
	    		{
	    			$status = '<span class="text-black-50">Dispute</span>';
                    $rate = '<a href="'.url('comments-seller').'/'.encrypt($row->buyer_id).'/'.$row->no.'"><i class="fas fa-envelope"></i></a>';
	    		}
                elseif($row->status == 7)
                {
                    $status = $confirm_sell;
                }
                else
                {
                    $status = '<span class="text-black-50">Lunas</span>';
                    $rate = '<a href="'.url('comments-seller').'/'.encrypt($row->buyer_id).'/'.$row->no.'"><i class="fas fa-envelope"></i></a>';
                }

	    		if($row->date_buy == null)
	    		{
	    			$date_buy = '-';
	    		}
	    		else
	    		{
	    			$date_buy = $row->date_buy;
	    		}

                //SHOW TRIAL TO MAKE DIFFERENT BETWEEN TRIAL AND NON TRIAL
                if($row->trial == 1)
                {
                    $trial = Lang::get('order.yes');
                }
                else
                {
                    $trial = '-';
                }

    			$data[] = [
    				'id'=>$row->id,
    				'no'=>$row->no,
    				'buyer'=>$row->buyer,
    				'amount'=>$price->pricing_format($row->amount),
    				'coin_fee'=>$price->pricing_format($row->coin_fee),
    				'kurs'=>$row->kurs,
    				'total'=>'Rp '.$price->pricing_format($total_price),
    				'created_at'=>$row->created_at,
    				'date_buy'=>$date_buy,
                    'trial'=>$trial,
                    'payment'=>$row->payment,
                    'rate'=>$rate,
    				'status'=>$status,
    			];
    		endforeach;
    	}

    	return view('seller.sell-content',['data'=>$data]);
    }

    // DISPLAY DETAIL SELL
    public function detail_sell($id)
    {
        $tr = Transaction::where([['id',$id],['status',1]])->first();

        // TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
        if(is_null($tr) || $tr->buyer_id == Auth::id())
        {
            return view('error404');
        }

        $user = User::find($tr->buyer_id);
        $pc = new Price;
        $cm = self::buyer_rate($tr->buyer_id);

        $check = '<i class="fas fa-times text-danger"></i>';
        $wrong = '<span class="text-success">-</span>';

        if($user->warning == 1)
        {
            $warning = $check;
        }
        else
        {
            $warning = $wrong;
        }

        if($user->suspend == 1)
        {
            $suspend = $check;
        }
        else
        {
            $suspend = $wrong;
        }

        

        $data = [
            'id'=>$tr->id,
            'no'=>$tr->no,
            'rate'=>$cm['star'],
            'star_float'=>$cm['star_float'],
            'buyer'=>$user->name,
            'warning'=>$warning,
            'suspend'=>$suspend,
            'coin'=>$pc->pricing_format($tr->amount),
            'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($tr->total),
            'link_comment'=>''.url('comments-seller').'/'.encrypt($tr->buyer_id).'',
        ];

        return view('seller.sell-detail',['row'=>$data]);
    }

    public static function buyer_rate($buyer_id)
    {
        $cm = Comment::selectRaw('AVG(rate) AS star')->where([['buyer_id',$buyer_id],['is_seller',1]])->first();
        
        $star_float = 0;
        $star = number_format((int)$cm->star, 1, '.', '');

        if(is_float($cm->star) == true)
        {
            $star_float = number_format((float)$cm->star, 1, '.', '');
            $star_float = $star_float - $star;
        }

        $data = [
            'star'=>$star,
            'star_float'=>$star_float,
        ];
        return $data;
    }

    // COMMENTS TO RATE BUYER
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
        
        $buyer = User::find(decrypt($user_id));
        if(is_null($buyer))
        {
            return view('error404');
        }

        return view('buyer.comments',['tr'=>$tr,'user_id'=>$buyer->id,'member'=>$buyer,'role'=>1,'invoice'=>$invoice]);
    }

    // DECISION FROM SELLER TO BUYER REQUEST
    public function seller_decision(Request $request)
    {
        $pc = new Price;
        $id = strip_tags($request->id);
        $act = strip_tags($request->act);
        $trans = Transaction::where([['seller_id',Auth::id()],['id',$id],['status',1]])->first();

        if(is_null($trans))
        {
            return response()->json(['err'=>1]);
        }

        // act : 1 = accept, 2 = refuse, 3 = block and refuse

        if($act == 1)
        {
           // MAIL TO BUYER IF ORDER HAS ACCEPTED
           $buy = new Buyer;
           $buy::notify_buyer($trans->no,$trans->buyer_id,$trans->id,$pc->pricing_format($trans->amount),$pc->pricing_format($trans->total));
           return self::save_seller_decision(2,$act,$trans->id);
        }
        else
        {
           self::notify_refuse_buyer($trans->no,$trans->buyer_id,$trans->id);
           return self::save_seller_decision(0,$act,$trans->id);
        }
    }

    public static function save_seller_decision($status,$act,$id)
    {
        $tr = Transaction::find($id);
        $tr->status = $status;
        $buyer_id = $tr->buyer_id;

        // IN CASE OF BLOCK AND REFUSE
        if($act == 2)
        {
            $tr->buyer_id = 0;
            $tr->kurs = 0;
            $tr->total = $tr->amount;
            $tr->date_buy = null;
        }

        // IN CASE OF BLOCK
        $user = User::find(Auth::id());
        if($act == 3)
        {
            if($user->blocked_buyer !== null)
            {
                $blocked = explode("|",$user->blocked_buyer);
                array_pop($blocked);
                array_push($blocked,$buyer_id."|");
                $blocked = implode("|",$blocked);
            }
            else
            {
                $blocked = $buyer_id."|";
            }
           
            $user->blocked_buyer = $blocked;

            // ALL TRANSACTION CANCELLED IF SELLER BLOCK BUYER
            $trs = Transaction::where([['seller_id',Auth::id()],['buyer_id',$buyer_id],['status',1]]);

            if($trs->get()->count() > 0)
            {
                foreach($trs->get() as $col):
                    $tru = Transaction::find($col->id);
                    $tru->buyer_id = 0;
                    $tru->kurs = 0;
                    $tru->date_buy = null;
                    $tru->total = $tru->amount;
                    $tru->status = 0;
                    $tru->save();
                endforeach;
            }
        }

        try
        {
            $tr->save();
            $user->save();
            $res['err'] = 0;
        }
        catch(QueryException $e)
        {
            //dd($e->getMessage());
            $res['err'] = 1;
        }
        
        return response()->json($res);
    }

    //NOTIFICATION FOR BUYER WHEN SELLER REFUSE REQUEST ORDER
    public static function notify_refuse_buyer($invoice,$buyer_id)
    {
        $msg = new Messages;
        $msg = $msg::buyer_notification($invoice,null);

        $buyer = User::find($buyer_id);
        $data = [
          'message'=>$msg,
          'phone_number'=>$buyer->phone_number,
          'email'=>$buyer->email,
          'obj'=>new BuyerEmail($invoice,null),
        ];

        $adm = new adm;
        $adm->notify_user($data);
    }

    // CONFIRMATION PAGE
    public function sell_confirm($id)
    {
    	$tr = Transaction::where([['id',$id],['seller_id',Auth::id()]])->whereIn('status',[7,4])->first();
    	$pc = new Price;

    	if(is_null($tr))
    	{
    		return view('error404');
    	}

    	$user = User::find($tr->buyer_id);

    	if($tr->upload == null)
    	{
    		$upload = '-';
    	}
    	else
    	{
    		$upload = '<img class="zoom" width="300" height="300" src="'.Storage::disk('s3')->url($tr->upload).'">';
    	}

    	$data = [
    		'id' => $tr->id,
    		'buyer_name' => $user->name,
    		'no' => $tr->no,
            'seller_dispute_id' => $tr->seller_dispute_id,
    		'upload'=> $upload,
    		'coin' => $pc->pricing_format($tr->amount),
    		'total' => $pc->pricing_format($tr->total),
            'status' => $tr->status,
    	];

        return view('seller.confirm',['row'=>$data,'pc'=>$pc]);
    }

    // SELLING CONFIRMATION
    public function confirm_selling(Request $request)
    {
    	$id_transaction = $request->id;
    	$trans = Transaction::find($id_transaction);

    	if(is_null($trans))
    	{
    		return response()->json(['err'=>1]);
    	}

    	$buyer = User::find($trans->buyer_id);

    	try
    	{
    		$trans->status = 3;
    		$trans->save();
    		$buyer->coin += $trans->amount;
    		$buyer->save();
    		$data['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		$data['err'] = 1;
    	}

    	return response()->json($data);
    }

    public function thank_you()
    {
    	return view('seller.thankyou-confirm-seller');
    }

    // DELETE SELL COIN FROM MARKETS
    public function del_sell(Request $request)
    {
    	$tr = Transaction::where([['id',$request->id],['seller_id',Auth::id()]])->first();
    	$res['err'] = 1;

        // PREVENT SUSPEND USER
        if(Auth::user()->status == 3)
        {
            return response()->json($res);
        }

    	if(!is_null($tr))
    	{
            $user = User::find(Auth::id());
            // RETURN COIN FROM TRANSACTION TO USER / WALLET
            // $total_tr_coin = $tr->coin_fee + $tr->amount;
            $user->coin += $tr->amount;

    		try{
                $user->save();
    			$tr->delete();
    			$res['wallet'] = $user->coin;
                $res['err'] = 0;
    		}
    		catch(QueryException $e)
    		{
    			// $e->getMessage();
    		}

    		// RETURN USER'S TRIAL IF THEY DELETED TJ
    		if($res['err'] == 0)
    		{
    			if($tr->trial == 1 && $user->membership == 'free')
    			{
    				$user->trial++;
    				$user->save();
    			}
    		}
    	}

    	return response()->json($res);
    }

    public function seller_dispute($id)
    {
        $tr = Transaction::where([['id',$id],['seller_id',Auth::id()],['seller_dispute_id','=',0]])->first();

        if(is_null($tr))
        {
            return view('error404');
        }
        return view('seller.seller-dispute',['tr'=>$tr]);
    }

    // GET ORDER NUMBER FROM TRANSACTION
    public static function get_order_invoice($invoice)
    {
    	$tr = Transaction::select('no')->where('no','LIKE','%'.$invoice.'%')->orderBy('id','desc');

    	$count = $tr->get()->count() + 1;
    	$invoice =  $invoice.'-'.sprintf('%03d', $count);

    	return $invoice;
    }

    // GET NAME FOR INVOICE
    private static function get_name($name)
    {
    	$first_letter = substr($name,0,1);
    	$last_letter = substr($name,-1,1);
    	$name = strtoupper($first_letter.$last_letter);
    	return $name;
    }

/**/
}
