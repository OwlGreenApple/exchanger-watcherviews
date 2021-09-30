<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Http\Controllers\OrderController;
use App\Models\User;
use App\Models\Transaction;
use App\Helpers\Price;
use App\Helpers\Api;
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
        $rate = $pc::get_rate();
        $coin_fee = ($coin * $fee)/100;
        $total_coin = $coin + $coin_fee;
        $total_price = round($rate * $coin);
        $seller_name = Auth::user()->name;
        $invoice_name = self::get_name($seller_name);

        $dt = Carbon::now();
        $str = $invoice_name.$dt->format('ymd');

        $tr = new Transaction;
        $order_number = self::get_order_invoice($str);
        $tr->seller_id = Auth::id();
        $tr->no = $order_number;
        $tr->kurs = $rate;
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
    	$tr = Transaction::where('transactions.seller_id','=',Auth::id())->leftJoin('users','transactions.buyer_id','=','users.id')->select('transactions.*','users.name as buyer')->orderBy('transactions.id','desc')->get();

    	if($tr->count() > 0)
    	{

    		foreach($tr as $row):
                $confirm_sell = '<a target="_blank" href="'.url("sell-confirm").'/'.$row->id.'" class="btn btn-info btn-sm">Konfirmasi</a>';
                
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
	    		}
	    		elseif($row->status == 1)
	    		{
	    			$status = '<span class="text-black">Menunggu konfirmasi pembeli  </span>';
	    		}
                elseif($row->status == 2)
                {
                    $status = $confirm_sell;
                }
                elseif($row->status == 4)
                {
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
	    		}
                else
                {
                    $status = '<span class="text-black-50">Lunas</span>';
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

	    		$price = new Price;
    			$data[] = [
    				'id'=>$row->id,
    				'no'=>$row->no,
    				'buyer'=>$row->buyer,
    				'amount'=>$price->pricing_format($row->amount),
    				'coin_fee'=>$price->pricing_format($row->coin_fee),
    				'kurs'=>$row->kurs,
    				'total'=>'Rp '.$price->pricing_format($row->total),
    				'created_at'=>$row->created_at,
    				'date_buy'=>$date_buy,
                    'trial'=>$trial,
                    'payment'=>$row->payment,
    				'status'=>$status,
    			];
    		endforeach;
    	}

    	return view('seller.sell-content',['data'=>$data]);
    }

    // CONFIRMATION PAGE
    public function sell_confirm($id)
    {
    	$tr = Transaction::where([['id',$id],['seller_id',Auth::id()]])->whereIn('status',[2,4])->first();
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
