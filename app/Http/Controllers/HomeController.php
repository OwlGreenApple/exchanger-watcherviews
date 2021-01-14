<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('home.home');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('home.profile',['user'=>$user]);
    }

    public function update_profile(Request $request)
    {
        $name = strip_tags($request->username);
        $phone = strip_tags($request->phone);
        $call = strip_tags($request->code_country);
        $password = strip_tags($request->password);
        $data_country = strip_tags($request->data_country);

        $user_phone = $call.$phone;

        try{
          $user = User::find(Auth::id());
          $user->name = $name;

          if(!empty($phone)):
            $user->phone = $user_phone;
            $user->code_country = $data_country;
          endif;

          if(!empty($password)):
            $user->password = Hash::make($password);
          endif;

          $user->save();
          $response['error'] = 0;
          $response['current_phone'] = $user->phone;
        }
        catch(QueryException $e)
        {
          //$e->getMessage();
          $response['success'] = 1;
        }

        return response()->json($response);
    }

    public function order_history()
    {
        $orders = Orders::where('user_id',Auth::id())->orderBy('id','desc')->get();
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

        return view('home.orders',['orders'=>$data]);
    }

    //THANK YOU PAGE FOR USER CONFIRM ORDER
    public function confirm_thank_you()
    {
      return view('home.thankyou-confirm-payment');
    }

/* end home controller class */
}