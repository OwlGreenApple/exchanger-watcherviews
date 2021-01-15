<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use App\Http\Controllers\OrderController as Shop;

class CoinsController extends Controller
{
    //DISPLAY BUY COINS PAGE
    public function index()
    {
       $user = Auth::user();
       return view('coins.index',['user'=>$user]);
    }

    //PURCHASING COINS
    public function purchase_coins(Request $request)
    {
      $user = Auth::user();
      $coins = strip_tags($request->coins);
      $pricing = getPackageRate($user->membership);

      $coins_len = strlen($coins);

      //VALIDATION TO PREVENT IF USER PUT 0 MORE THAN 9 DIGITS (HUNDRED THOUSANDS)
      if($coins_len > 9)
      {
        $getlen =  9 - $coins_len;
        $coins = substr($coins,0,$getlen);
      }

      $coin = (int)$coins;

      //VALIDATION MINIMUM COINS
      if($coin < 5*pow(10,5)){
        $result['msg'] = 2;
        $result['message'] = "Minimum coins for purchasing is 500,000";
        return response()->json($result);
      }

      //VALIDATION MULTIPLIED 100.000 COINS
      $check_coin = $coin % pow(10,5);
      if($check_coin <> 0)
      {
        $result['msg'] = 2;
        $result['message'] = "The amount of coins must multiplied of 100,000";
        return response()->json($result);
      }

      //VALIDATION FOR MAX COINS
      if($coin > 999900000)
      {
        $result['msg'] = 2;
        $result['message'] = "The maximum amount of coins is 999,900,000";
        return response()->json($result);
      }

      $pricing = (int)$pricing;
      $totalprice = ($coin/pow(10,5)) * $pricing;

      $order = array(
        "price"=>$pricing,
        "namapaket"=>'buy-coins',
        "idpaket" => 0,
        "purchased_coins"=>$coin,
        "total"=> $totalprice,
      );

      $shop = new Shop;
      return $shop->payment_login($order);
    }

    /**** EXCHANGE ****/

    public function exchange()
    {
      $user = Auth::user();
      return view('coins.exchange',['user'=>$user]);
    }

/*end class*/
}
