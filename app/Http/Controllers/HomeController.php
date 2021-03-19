<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Orders;
use App\Models\Transaction;
use App\Models\Contacts;
use App\Http\Controllers\Auth\RegisterController as Reg;
use Storage, Cookie, Validator, Session;

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

    //DISPLAY ORDER HISTORY
    public function order_history($status)
    {
        /*
          - membership package
          - coin package
        */
        if($status == 'membership-history')
        {
          $orders = Orders::where([['user_id',Auth::id()],['package','<>','buy-coins']])->orderBy('id','desc')->get();
          $label = 'Upgrade Membership History';
        }
        elseif($status == 'coin-history')
        {
          $orders = Orders::where([['user_id',Auth::id()],['package','=','buy-coins']])->orderBy('id','desc')->get();
          $label = 'Coin Order History';
        }
       
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

        return view('home.orders',['orders'=>$data,'label'=>$label,'stlabel'=>$status]);
    }

    //THANK YOU PAGE FOR USER CONFIRM ORDER
    public function confirm_thank_you()
    {
      return view('home.thankyou-confirm-payment');
    }

    //REFERRAL
    public function referral()
    {
       $user = Auth::user();      
       if($user->referral_link == null || $user->referral_link == "")
       {
          $ref_link = $this->generate_referral_link();//generate referal link when user visit referal page       
       }
       else
       {
          $ref_link = $user->referral_link;
       }

      $transaction = Transaction::where([['user_id','=',$user->id],['source','LIKE','%referral-gift-user%']])->get();

       $data = [
        'user'=>$transaction,
        'referral_link'=>url('/referral-reg')."/".$ref_link
       ];

       return view('referral.index',$data);
    }

    //CREATE USER REFERRAL LINK
    public function generate_referral_link()
    {
        $userid = Auth::id();
        $user = User::find($userid);
        $user->referral_link = $this->createRandomLinkName();

        try{
           $user->save();
          /* $data['msg'] = 0;
           $data['link'] = url('/referral-reg')."/".$user->referral_link;*/
        }
        catch(QueryException $e)
        {
          // $e->getMessage();
          // $data['msg'] = 1;
        }
        return $user->referral_link;
    }

    public function referral_register($referral_link)
    {
        $ref = User::where('referral_link',$referral_link)->first();

        if(!is_null($ref))
        {
          $this->setCookie($ref->name,$ref->id);
        }
        else
        {
          return redirect('/register');
        }

        //IF PREVIOUS COOKIE AVAILABLE USE THIS TO REPLACE WITH NEW COOKIE
        if(Cookie::get('referral') !== null && Cookie::get('refid') !== null)
        {
            $referral = [
              'ref_name'=>$ref->name,
              'ref_id'=>$ref->id
            ];
            return view('auth.register',$referral);
        }

        //IF NEW REFERRAL WITH NEW COOKIE
        if(Cookie::get('referral') == null && Cookie::get('refid') == null)
        {
          $referral = [
            'ref_name'=>$ref->name,
            'ref_id'=>$ref->id
          ];
        }
        else
        {
          $referral = [
            'ref_name'=>Cookie::get('referral'),
            'ref_id'=>Cookie::get('refid')
          ];
        }

        return view('auth.register',$referral);
    } 

    private function setCookie($referral_link,$referral_id)
    {
      if(!empty($referral_link))
      {
          Cookie::queue(Cookie::make('referral', $referral_link, 1440*30));
          Cookie::queue(Cookie::make('refid', $referral_id, 1440*30));
      } else {
          return redirect('/');
      }
    }

    //GENERATE USER REFERRAL LINK
    public function createRandomLinkName(){

        $generate = $this->generateRandomLinkName();
        $list = User::where('referral_link','=',$generate)->first();

        if(is_null($list)){
            return $generate;
        } else {
            return $this->createRandomLinkName();
        }
    }

    /* create random list name */
    public function generateRandomLinkName(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 8);
    }

    /*TRANSACTION*/
    public function transaction()
    {
      $trans = Transaction::where('user_id',Auth::id())->orderBy('id','desc')->get();
      return view('home.transaction',['transaction'=>$trans]);
    }

    /*CONTACT*/
    public function contact()
    {
      return view('home.contact');
    }

    // SAVE CONTACT FROM USER
    public function save_contact(Request $request)
    {
      $user = Auth::user();
      $filter = [
        'judul'=>strip_tags($request->title),
        'pesan'=>strip_tags($request->message)
      ];

      if($user->is_admin == 0)
      {
        $rules['judul'] = ['required','string','min:4','max:100'];
      }

      $rules['pesan'] = ['required','string','min:4','max:190'] ;
      

      $validator = Validator::make($filter,$rules);
      if($validator->fails() == true)
      {
        $err = $validator->errors();
        $response = [
            'error'=>2,
            'title'=>$err->first('judul'),
            'message'=>$err->first('pesan'),
        ];
      }
      else
      {

        //USER SEND QUESTION
        if($user->is_admin == 0)
        {
          $ct = new Contacts;
          $ct->user_id = Auth::id();
          $ct->title = $filter['judul'];
          $ct->message = $filter['pesan'];
        }
        else
        {
          //ADMIN REPLY TO USER
          $ct = Contacts::find($request->id_contacts);
          $ct->reply = $filter['pesan'];
        }

        try
        {
          $ct->save();
          $response['error'] = 0;
        }
        catch(QueryException $e)
        {
          $response['error'] = 1;
        }
      }

      return response()->json($response);
    }

    //DATATABLE FOR CONTACT PAGE
    public function contact_table()
    {
      $contacts = Contacts::where('contacts.user_id',Auth::id())->orderBy('id','desc')->get();
      return view('home.contact-table',['data'=>$contacts]);
    }

/* end home controller class */
}