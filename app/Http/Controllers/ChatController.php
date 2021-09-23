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

class ChatController extends Controller
{
  public function room($tr_id)
  {

  	return view('chats.chat');

  	$valid = false;
  	$tr = Transaction::find($tr_id);

  	if(is_null($tr))
  	{
  		return view('error404');
  	}

  	if($tr->seller_id == Auth::id() || $tr->buyer_id == Auth::id())
  	{
  		$valid = true;
  	}

  	if($valid == true)
  	{
  		$chats = Chat::where('trans_id',$tr_id)->get();
  		dd($chats);
  		/*
			display chat page here
  		*/
  	}
  	else
  	{
  		return view('error404');
  	}
  }  

/**/
}
