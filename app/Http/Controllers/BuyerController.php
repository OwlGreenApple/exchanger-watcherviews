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

class BuyerController extends Controller
{
    public function buying_page()
    {
        return view('buyer.buy',['pc'=>new Price]);
    }

    public function buyer_table()
    {
    	$tr = Transaction::where([['transactions.status','=',0],['users.status','>',0]])->join('users','users.id','=','transactions.seller_id')->select('transactions.*','users.status')->get();
    	$pc = new Price;

    	if($tr->count() > 0)
    	{
    		foreach($tr as $row):
    			$data[] = [
    				'id'=>$row->id,
    				'no'=>$row->no,
    				'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($row->total),
    				'coin'=>$pc->pricing_format($row->amount),
    				'seller'=>$row->seller_id,
    			];
    		endforeach;
    	}

    	return view('buyer.buy-table',['data'=>$data]);
    }

    public function detail_buy($invoice)
    {
    	$tr = Transaction::where('no',$invoice)->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr) || $tr->seller_id == Auth::id())
    	{
    		return view('error404');
    	}

    	$user = User::find($tr->seller_id);
    	$pc = new Price;

    	$data = [
    		'id'=>$tr->id,
    		'no'=>$tr->no,
    		'seller'=>$user->name,
    		'coin'=>$pc->pricing_format($tr->amount),
    		'total'=>Lang::get('custom.currency').$pc->pricing_format($tr->total),
    	];

        return view('buyer.buy-detail',['row'=>$data]);
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
    	$data = [
    		'id'=>$tr->id,
    		'no'=>$tr->no,
    		'seller'=>$user->name,
    		'coin'=>$pc->pricing_format($tr->amount),
    		'total'=>Lang::get('custom.currency').' '.$pc->pricing_format($tr->total),
    	];

        return view('buyer.buy-deal',['row'=>$data,'user'=>$user]);
    }

    public function buyer_deal(Request $request)
    {
    	$id = $request->id;
    	$tr = Transaction::find($id);

    	if(is_null($tr))
    	{
    		return response()->json(['err'=>1]);
    	}

    	try
    	{
    		$tr->buyer_id = Auth::id();
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
    	$tr = Transaction::where('buyer_id',Auth::id())->get();
    	$comments = '-';

    	if($tr->count() > 0)
    	{
    		foreach($tr as $row)
    		{
    			if($row->status == 1)
    			{
    				$status = '<a target="_blank" href="'.url('buyer-confirm').'/'.$row->no.'" class="btn btn-primary btn-sm">Konfirmasi</a>';
    			}

    			if($row->status == 2)
    			{
    				$status = '<span class="text-info">Menunggu konfirmasi dari seller</span>';
    			}

    			if($row->status == 3)
    			{
    				$status = '<span class="text-black-50">Lunas</span>';
    				$comments = '<a href="'.url('comments').'"><i class="far fa-envelope"></i></a>';
    			}
    		
    			$data[] = [
    				'id'=>$row->id,
    				'date'=>$row->created_at,
    				'no'=>$row->no,
    				'coin'=>$row->amount,
    				'kurs'=>$row->kurs,
    				'price'=>$row->total,
    				'comments'=>$comments,
    				'status'=>$status,
    			];
    		}

    		// dd($data);
    	}

        return view('buyer.buy-content',['data'=>$data]);
    }

    public function buyer_confirm($invoice)
    {
    	$tr = Transaction::where('no',$invoice)->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr))
    	{
    		return view('error404');
    	}

        return view('buyer.buyer-confirm',['row'=>$tr]);
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

    	try
    	{
    		$tr->upload = $proof;
    		$tr->date_buy = Carbon::now();
    		$tr->buyer_comment = strip_tags($request->note);
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

    public function comments()
    {
        return view('buyer.comments');
    }

    public function buyer_dispute()
    {
        return view('buyer.buyer-dispute');
    }

/*end class*/
}
