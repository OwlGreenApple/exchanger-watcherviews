<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Orders;
use Session, DB;

class OrderController extends Controller
{
    // DISPLAY PRICING PAGE
    public function index()
    {
      //delete session if user want to change order
      if(session('order') <> null)
      {
        Session::forget('order');
      }

      return view('pricing.checkout');
    }

    //CHECKOUT PAYMENT
    public function payment(Request $request)
    {
      $idpackage = strip_tags($request->idpackage);

      //PREVENT USER CHANGE PACKAGE ID
      if(validationPackage($idpackage) == false)
      {
        return response()->json(['msg'=>2]);
      }
     
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
    private function payment_checkout($order)
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

    //DISPLAY SUMMARY PAGE (PAGE IF USER HAVEN'T LOGIN YET)
    public function summary()
    {
      //prevent user to reach summary page when order is not set
      if(session('order') == null)
      {
        return redirect('pricing');
      }

      return view('pricing.summary');
    }

    // IF USER HAS LOGGED IN
    private function payment_login($data,$log = null)
    {
      $dt = Carbon::now();
      $str = 'ACT'.$dt->format('ymd').'-'.$dt->format('Hi');
      $model = new Orders;
      $order_number = $this->autoGenerateID($model, 'no_order', $str, 3, '0');

      try{
        $order = new Orders;
        $order->user_id = Auth::id();
        $order->no_order = $order_number;
        $order->package_id = (int)$data['idpaket'];
        $order->package = $data["namapaket"];
        $order->package_title = $data["namapaket"];
        $order->price = $data["price"];
        $order->total = $data["total"];
        $order->save();
        $response['msg'] = 0;
      }
      catch(QueryException $e)
      {
        // echo $e->getMessage();
        $response['msg'] = 1;
      }

      //if user order after login then $log = null
      if($log == null)
      {
        return response()->json($response);
      }
    }

    public function submit_summary(Request $request)
    {
      $user = Auth::user();

      if(session('order') == null)
      {
          return redirect('pricing');
      }

      $data = [
        "idpaket"=> session('order')['idpaket'],
        "namapaket"=> session('order')['namapaket'],
        "namapakettitle"=> session('order')['namapaket'],
        "phone"=>$user->phone,
        "price"=> session('order')['price'],
        "total"=>session('order')['total']
      ];

      $order = $this->payment_login($data,1);
      return view('pricing.thankyou');    
    }

    public function thankyou()
    {
      return view('pricing.thankyou');   
    }

    //GENERATE ID FOR NO_ORDER
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
