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
    	$coin = $request->tr_coin;
    	$pc = new Price;
        $fee = $pc->check_type(Auth::user()->membership)['fee'];
        $rate = $pc::get_rate();
        $coin_fee = ($coin * $fee)/100;
        $total_coin = $coin - $coin_fee;
        $total_price = $rate * $total_coin;
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
        $tr->amount = $total_coin;
        $tr->total = $total_price;

        try
        {
        	$tr->save();
        	$data['err'] = 0;
        }
        catch(QueryException $e)
        {
        	//$e->getMessage()
        	$data['err'] = Lang::get('custom.failed');
        }

        return response()->json($data);
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
