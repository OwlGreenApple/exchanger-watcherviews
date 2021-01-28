<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use App\Models\Transaction;
use App\Models\Contacts;
use Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    // GET ORDER DATA
    private static function get_order_data()
    {
      $orders = Orders::orderBy('id','desc')->get();
       $data = array();

        if($orders->count() > 0)
        {
          foreach($orders as $row)
          {
            if($row->buktibayar == null || $row->buktibayar == ""){
              $buktibayar =  0;
            }
            else
            {
              $buktibayar = $row->buktibayar;
            }

            $data[] = array(
              'id'=>$row->id,
              'no_order'=>$row->no_order,
              'package'=>$row->package,
              'purchased_coins'=>$row->purchased_coins,
              'price'=>$row->price,
              'total'=>$row->total,
              'created_at'=>$row->created_at,
              'buktibayar'=>$buktibayar,
              'note'=>$row->note,
              'status'=>$row->status
            );
          }
        }

        return $data;
    }

    //DISPLAY ORDER PAGE
    public function index()
    {
       $data = self::get_order_data();
       return view('admin.index',['orders'=>$data]);
    }

    //CONFIRM USERS ORDER AND DISPLAY TABLE PAGE
    public function confirm_order(Request $request)
    {
       $order = Orders::find($request->id_confirm);
       $order->status = 2;
       $user = User::find($order->user_id);

       //MEMBERSHIP PACKAGE
       if($order->package_id <> 0)
       {
          if($user->valid_until == "" || $user->valid_until == null && ($user->membership == null || $user->membership == ""))
          {
             $valid_until = Carbon::now()->addYear();
          }
          else
          {
             $valid_until = Carbon::parse($user->valid_until)->addYear();
          }
          
          $user->membership = $order->package;
          $user->credits += getPackage()[$order->package_id]['bc'];
          $user->valid_until = $valid_until;

            //COINS TRANSACTIONS
          $trans = new Transaction;
          $trans->user_id = $user->id;
          $trans->debit = getPackage()[$order->package_id]['bc'];
          $trans->source = "bonus-coins-membership";
          $trans->save();
       } 

       // BUYING COINS
       if($order->package_id == 0)
       {
          $user->credits += $order->purchased_coins;

          //referral gift
          $ref_id = $user->referral_id; 
          if($ref_id > 0)
          {
             $purchased_coins = $order->purchased_coins;
             $this->gift_referral($ref_id,$purchased_coins,$user->name);
          }

          //COINS TRANSACTIONS
          $trans = new Transaction;
          $trans->user_id = $user->id;
          $trans->debit = $order->purchased_coins;
          $trans->source = "buying-coins";
          $trans->save();
       }

       try
       {
          $user->save();
          $order->save(); 
       }
       catch(QueryException $e)
       {
          //$e->getMessage();
       }

       $data = self::get_order_data();
       return view('admin.table',['orders'=>$data]);
    }

    //IF REFERRAL USER BUYING COINS THEN REFERRER GET 10% BONUS COINS
    private function gift_referral($ref_id,$purchased_coins,$userbuy)
    {
      $refuser = User::find($ref_id);

      //PREVENT IF REFERRER USER BANNED (if someday this feature add on) OR DELETED.
      if(is_null($refuser))
      {
        return;
      }

      $gift_coins = $purchased_coins / 10;
      $refuser->credits += $gift_coins;

      try
      {
        $refuser->save();
        //coins transaction
        $trans = new Transaction;
        $trans->user_id = $ref_id;
        $trans->debit = $gift_coins;
        $trans->source = "referral-gift-buying-".$userbuy;
        $trans->save();
      }
      catch(QueryException $e)
      {
        //$e->getMessage();
      }
    }

    public function user_contacts()
    {
      return view('admin.contact');
    }

    //DISPLAY CONTACT FROM USER
    public function user_contacts_table()
    {
      $contacts = Contacts::where('reply','=',null)->orderBy('id','desc')->get();
      return view('admin.contact-table',['data'=>$contacts]);
    }

/* END ADMIN CONTROLLER */
}
