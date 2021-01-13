<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use Storage;

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
       try 
       {
          $order->status = 2;
          $order->save();
       }
       catch(QueryException $e)
       {
          //$e->getMessage();
       }

       $data = self::get_order_data();
       return view('admin.table',['orders'=>$data]);
    }

/* END ADMIN CONTROLLER */
}
