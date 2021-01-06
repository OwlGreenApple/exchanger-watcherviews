<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Orders;
use session;

class OrderController extends Controller
{
    // DISPLAY PRICING PAGE
    public function index()
    {
      if(session('order') <> null)
      {
        session::forget('order');
      }

      return view('pricing.checkout');
    }

    //CHECKOUT PAYMENT
    public function payment(Request $request)
    {
      $idpackage = strip_tags($request->idpackage),
     
      $price = getPackage()[$idpackage]['price'];
      $package = getPackage()[$idpackage]['package'];
      $total = (int)$price;

      $order = array(
        "price"=>$price,
        "namapaket"=>$package,
        "idpaket" => $idpackage,
        "total"=> $total,
      );

      if(Auth::id() == null)
      {
         return $this->payment_checkout($order);
      }
      else
      {
        return $this->payment_login($order);
      }
    }

    // IF USER HAVEN'T ACCOUNT / NOT LOGGED IN
    private function payment_checkout($data)
    {
      // ditaruh ke session dulu
      if(session('order') == null)
      {
        session(['order'=>$order]);
      }

      if(count(session('order')) > 0)
      {
        return response()->json(['msg'=>0,'sm'=>1]);
      }
    }

    public function summary()
    {
      if(session('order') == null)
      {
        return redirect('pricing');
      }

      return view('pricing.summary');
    }

    // IF USER HAS LOGGED IN
    private function payment_login($data)
    {
      $dt = Carbon::now();
      $str = 'ACT'.$dt->format('ymd').'-'.$dt->format('Hi');
      $model = new Order;
      $order_number = $this->autoGenerateID($model, 'no_order', $str, 3, '0');

      try{
        $order = new Orders;
        $order->user_id = Auth::id();
        $order->no_order = $order_number;
        $order->package_id = $data['idpaket'];
        $order->package = $data["namapaket"];
        $order->package_title = $data["namapaket"];
        $order->price = $data["price"];
        $order->total = $data["total"];
        $order->save();
        $data['msg'] = 0;
      }
      catch(QueryException $e)
      {
        //$e->getMessage();
        $data['msg'] = 1;
      }

      return response()->json($data);
    }

    private static function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
    {
      $tb = $model->select(DB::raw("substr(".$field.", ".strval(strlen($search)+1).") as lastnum"))
                  ->whereRaw("substr(".$field.", 1, ".strlen($search).") = '".$search."'")
                  ->orderBy('id', 'DESC')
                  ->first();
      if ($tb == null){
        $ctr = 1;
      }
      else{
        $ctr = intval($tb->lastnum) + 1;
      }
      return $search.'-'.str_pad($ctr, $pad_length, $pad_string, STR_PAD_LEFT);
    }

/* end of controller class */
}
