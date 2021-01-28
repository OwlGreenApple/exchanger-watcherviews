<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use App\Models\Exchange;
use App\Models\Drips;
use App\Models\Transaction;
use App\Http\Controllers\OrderController as Shop;
use Carbon\Carbon;

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

    //SUBMIT EXCHANGE COINS
    public function submit_exchange(Request $request)
    {
      // dd($request->all());
      $views = strip_tags((int)str_replace(".","",$request->views));
      $drip = strip_tags((int)str_replace(".","",$request->runs));
      $id_exchange = strip_tags($request->exchange);
      $ytlink = strip_tags($request->link_video);

      $success = false;
      $rate = getExchangeRate($id_exchange);
      $total_views = $views * $drip;
      
      $credit = $total_views * ($rate['coins']/1000);

      try
      {
        $exc = new Exchange;
        $exc->user_id = Auth::id();
        $exc->duration = $rate['duration'];
        $exc->coins_value = $rate['coins'];
        $exc->id_exchange = $id_exchange;
        $exc->yt_link = $ytlink;
        $exc->views = $views;
        if($request->drip == "1"):
          $exc->drip = $drip;
        endif;
        $exc->total_coins = $credit;
        $exc->total_views = $total_views;
        $exc->save();
        $exchange_id = $exc->id;
      }
      catch(QueryException $e)
      {
        // $e->getMessage();
        $data['msg'] = 1;
        return response()->json($data);
      }

      //cut user coins if eligible
      try
      {
        $user = User::find(Auth::id());
        $user->credits -= $credit;
        $user->save();
        $data['msg'] = 0;
        $data['credit'] = $user->credits;
        $success = true;
      }
      catch(QueryException $e)
      {
        // $e->getMessage();
        $data['msg'] = 1;
      }

       //PUT EXCHANGE TO TRANSACTION
      if($success == true)
      {
        $trans = new Transaction;
        $trans->user_id = Auth::id();
        $trans->kredit = $credit;
        $trans->source = "exchange-coins";
        $trans->save();
      }

      //SEND TO WATCHERVIEWS API IF CASE VIDEO NOT DRIP
      if($request->drip !== "1")
      {
        $api = [
                'ytlink'=>$ytlink,
                'duration'=>$rate['duration'],
                'views'=>$total_views,
              ];
        self::add_youtube_link($api);
      }

      //if user request drip and membership
      if($request->drip == "1"):
          $ret = [
            'credit'=>$data['credit'],
            'exchange_id'=>$exchange_id
          ];
          return $this->fetch_drip($ret);
      endif;

      return response()->json($data);
    }

    //DATATABLE EXCHANGE COINS
    public function exchange_table()
    {
      $exc = Exchange::where('user_id',Auth::id())->orderBy('id','desc')->get();
      $data = [];

      if($exc->count() > 0)
      {
        foreach($exc as $row)
        {
          if($row->drip > 0)
          {
            $process = Drips::where([['exchange_id',$row->id],['status','=',1]])->get()->count();
          }
          else
          {
            $process = null;
          }

          $data[] = (object)[
            'duration'=>$row->duration,
            'coins_value'=>$row->coins_value,
            'yt_link'=>$row->yt_link,
            'views'=>$row->views,
            'drip'=>$row->drip,
            'total_coins'=>$row->total_coins,
            'total_views'=>$row->total_views,
            'created_at'=>$row->created_at,
            'process'=>$process
          ];
        }
      }

      return view('coins.exchange-table',['data'=>$data]);
    }

    //FETCH USER DRIP INTO DATABASE FOR CRON JOB SCHEDULES
    private function fetch_drip(array $ret)
    {
      $exc = Exchange::find($ret['exchange_id']);
      $current_time = Carbon::now();
      $data['msg'] = 0;

      if(!is_null($exc))
      {
        for($x=1;$x<=$exc->drip;$x++):
           //TIME DRIP (4 HOURS 15 MINUTES AND 5 HOURS RANDOMLY) 
          $drip = [255,300];
          $random = mt_rand(0,1);
          $drip = $drip[$random];
          $current_time = $current_time->addMinutes($drip);

          $drp = new Drips;
          $drp->exchange_id = $ret['exchange_id'];
          $drp->schedule = $current_time;
          $drp->drip = $drip;
          $drp->save();
        endfor;
        $data['credit'] = $ret['credit'];
      }
      else{
        $data['msg'] = 1;
      }

      return response()->json($data);
    }

    /*** API ***/

    public static function add_youtube_link(array $cels)
    {
      $curl = curl_init();
      $url = 'https://watcherviews.com/dashboard/add-video-fromcelebfans';
      $data = array(
          'key_celebfans'=>'f6a055c556be9d36a68ce3f632f25d70b7168399dec581ae98c7f3ea3c950bc5787c6e3310bf32d3',
          'coin_get' => self::coin_get($cels['duration']),
          'link' => $cels['ytlink'],
          'time' => $cels['duration'],
          'maximum_watch' => $cels['views'], //view
          'description' => 'add video from celebfans'
      );

      $data_string = json_encode($data);
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 360);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
      ));

      $response = curl_exec($ch);
      $err = curl_error($ch);
      curl_close($ch);

      // dd($response);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        return json_decode($response,true);
      }
    }

    //FOR COIN GET getExchangeRate => \Helpers\CustomHelper.php
    private static function coin_get($sec)
    {
        $data = [
          getExchangeRate(1)['duration']=>400,
          getExchangeRate(2)['duration']=>600,
          getExchangeRate(3)['duration']=>800,
          getExchangeRate(4)['duration']=>1200,
          getExchangeRate(5)['duration']=>1600,
          getExchangeRate(6)['duration']=>2000,
          getExchangeRate(7)['duration']=>2400
        ];

        return $data[$sec];
    }

/*end class*/
}
