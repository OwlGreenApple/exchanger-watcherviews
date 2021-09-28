<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Models\Chat;
use App\Models\User;
use App\Models\Transaction;
use App\Helpers\Price;
use App\Helpers\Api;
use Carbon\Carbon;
use Validator;

class ChatController extends Controller
{
  public function room($tr_id)
  {
  	$tr = Transaction::find($tr_id);
  	$auth = Auth::user();
  	$valid = false;

  	if(is_null($tr) || $tr->status !== 4)
  	{
  		return redirect('error');
  	}

  	if($tr->seller_id == Auth::id() || $tr->buyer_id == Auth::id() || $auth->is_admin > 0)
  	{
  		$valid = true;
  	}

  	if($valid == true)
  	{
  		return view('chats.chat',['tr_id'=>$tr_id,'invoice'=>$tr->no]);
  	}
  	else
  	{
  		return redirect('error');
  	}
  }  

  // AJAX BASED DATA
  public function display_chat(Request $request)
  {
  	$tr = Transaction::find($request->tr_id);
  	$auth = Auth::user();

  	$chats = Chat::where('chats.trans_id',$tr->id)->join('users','users.id','=','chats.user_id')->select('chats.*','users.name')->get();
  		return view('chats/chat-data',['data'=>$chats]);
  }

  public function save_chat(Request $request)
  {
  		$trans_id = $request->tr_id;
  		$comments = strip_tags($request->comments);

  		$rules = ['comments'=>['required','max:255']];
  				
  		$validator = Validator::make($request->all(),$rules);

  		if($validator->fails() == true)
  		{
  			$err = $validator->errors();
  			$errors = [
  				'err'=>'validation',
  				'comments'=>$err->first('comments')
  			];

  			return response()->json($errors);
  		}

  		$tr = Transaction::find($trans_id);

	  	if(is_null($tr))
	  	{
	  		return response()->json(['err'=>1]);
	  	}

	  	// to prevent user make comment on retain page after admin end chat
	  	if($tr->status !== 4)
	  	{
	  		return response()->json(['err'=>2]);
	  	}

	  	$auth = Auth::user();

	  	if($auth->is_admin > 0)
	  	{
	  		$role = 0;
	  	}
	  	elseif($tr->buyer_id == $auth->id)
	  	{
	  		$role = 1;
	  	}
	  	else
	  	{
	  		$role = 2;
	  	}

	  	$chat = new Chat;
	  	$chat->trans_id = $trans_id;
	  	$chat->user_id = Auth::id();
	  	$chat->comments = $comments;
	  	$chat->role = $role;

	  	try
	  	{
	  		$chat->save();
	  		$ret['err'] = 0;
	  	}
	  	catch(QueryException $e)
	  	{
	  		$ret['err'] = 1;
	  	}

	  	return response()->json($ret);
  }

/**/
}
