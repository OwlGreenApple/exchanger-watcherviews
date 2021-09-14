<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Http\Controllers\OrderController;
use App\Models\User;
use App\Models\Comment;
use App\Models\Transaction;
use App\Helpers\Price;
use App\Helpers\Api;
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
    	$paginate = 1;
    	$tr = Transaction::where([['transactions.status','=',0],['users.status','>',0]])->join('users','users.id','=','transactions.seller_id')->select('transactions.*','users.status')->paginate($paginate);

    	$pc = new Price;

    	if($tr->count() > 0)
    	{
    		$rate = ' ';
    		foreach($tr as $row):
    			$seller = User::find($row->seller_id);
    			$cm = Comment::selectRaw('AVG(rate) AS star')->where('seller_id',$row->seller_id)->first();
    			$star = round($cm->star);

    			if($star == 0)
    			{
    				$rate = '-';
    			}
    			else
    			{
    				for($x=0;$x<$star;$x++)
    				{
    					$rate.='<i class="fas fa-star"></i>';
    				}
    			}

    			$data[] = [
    				'id'=>$row->id,
    				'no'=>$row->no,
    				'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($row->total),
    				'coin'=>$pc->pricing_format($row->amount),
    				'seller_name'=>$seller->name,
    				'seller'=>$row->seller_id,
    				'rate'=>$rate,
    			];
    		endforeach;
    	}

    	return view('buyer.buy-table',['data'=>$data,'paginate'=>$tr]);
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
    	$pc = new Price;

    	if($tr->count() > 0)
    	{
    		foreach($tr as $row)
    		{
    			if($row->status == 1)
    			{
    				$status = '<a target="_blank" href="'.url('buyer-confirm').'/'.$row->no.'" class="btn btn-primary btn-sm">Konfirmasi</a>';
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
    			else
    			{
    				$comments = '-';
    			}

    			$seller = User::find($row->seller_id);
    			$data[] = [
    				'id'=>$row->id,
    				'seller'=>$seller->name,
    				'date'=>$row->created_at,
    				'no'=>$row->no,
    				'coin'=>$pc->pricing_format($row->amount),
    				'kurs'=>$row->kurs,
    				'price'=>Lang::get('custom.currency').' '.$pc->pricing_format($row->total),
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
    	if(is_null($tr) || $tr->status > 1)
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

    public function comments($invoice)
    {
    	$tr = Transaction::where('no',$invoice)->first();

    	// TO AVOID IF USER DELIBERATELY PUT HIS PRODUCT
    	if(is_null($tr))
    	{
    		return view('error404');
    	}

        return view('buyer.comments',['tr'=>$tr]);
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
    				'comments'=>$row->comments,
    				'rate'=>$row->rate,
    				'created_at'=>$row->created_at,
    			];
    		}

    		return view('buyer.comments-data',['data'=>$data]);
    	}
    }

    public function buyer_dispute()
    {
        return view('buyer.buyer-dispute');
    }

/*end class*/
}
