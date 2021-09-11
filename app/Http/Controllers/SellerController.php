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
        return view('home.sell',['lang'=>new Lang,'pc'=>new Price,'fee'=>$fee]);
    }

    public function selling_save(Request $request)
    {
    	$coin = str_replace(".","",$request->tr_coin);
    	$coin = (int)$coin;

    	$pc = new Price;
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
    			if($row->status == 0)
	    		{
	    			$status = '<a data-id="'.$row->id.'" class="text-danger del_sell"><i class="fas fa-trash-alt"></i></a>';
	    		}
	    		elseif($row->status == 1)
	    		{
	    			$status = '<a target="_blank" href="'.url("transfer").'/'.$row->id.'" class="btn btn-info btn-sm">Konfirmasi</a>';
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
    				'status'=>$status,
    			];
    		endforeach;
    	}

    	return view('seller.sell-content',['data'=>$data]);
    }

    // DELETE SELL COIN FROM MARKETS
    public function del_sell(Request $request)
    {
    	$tr = Transaction::find($request->id);
    	$user = User::find(Auth::id());
    	$res['err'] = 1;

    	if(!is_null($tr))
    	{
    		try{
    			$tr->delete();
    			$res['err'] = 0;
    		}
    		catch(QueryException $e)
    		{
    			// $e->getMessage();
    		}

    		// RETURN USER'S TRIAL IF THEY DELETED TJ
    		if($res['err'] == 0)
    		{
    			if($user->trial < 3 && $user->membership == 'free')
    			{
    				$user->trial++;
    				$user->save();
    			}
    		}
    	}

    	return response()->json($res);
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
