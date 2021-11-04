<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Atcoupons;
use App\Http\Controllers\Auth\RegisterController as Reg;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Price;
use Carbon\Carbon;

class ApiController extends Controller
{
	// if user from watcherviews buy package
 	public function promotion(Request $request)
 	{
 		$key = 'A9dsF5_jK0_e1F3pDeXx10M3V2';
 		$request_key = $request->key;

 		if($request_key !== $key)
 		{
 			return false;
 		}

 		$email = $request->email;
 		$name = $request->name;
 		$phone_number = $request->phone_number;
 		$gender = $request->gender;

    /*$email = "apitest@mail.com";
    $name = "aaaapi";
    $phone_number = "62899999";
    $gender = "male";*/

 		$user = User::where('email',$email)->first();

 		if(is_null($user))
 		{
 			$data = [
 				'email'=>$email,
 				'username'=>$name,
 				'phone_api'=>$phone_number,
 				'gender'=>$gender,
 				'is_promote'=>1,
 			];
 			$reg = new Reg;
 			$reg->register_api($data);
 		}
 		else
 		{
 			// REGISTERED USER DIDN'T GET PROMOTE
 			if($user->status > 0 && $user->is_promote == 0)
 			{
        $user->is_promote = 1;
 				$user->save();
 			}
 		}
 	}

 	// user redeem coin to activrespon and omnilinks
 	public function exchange_coin(Request $request)
 	{
        $user = User::find(Auth::id());
        $pc = new Price;

 		if($request->diskon_value == 2)
 		{
 			$discount = 200000;
            $coin = 2000000;
 		}
 		else
 		{
 			$discount = 100000;
            $coin = 1000000;
 		}

 		if($user->coin < $coin)
 		{
            $ret['api'] = $request->api;
 			return response()->json($ret);
 		}

        // DETERMINE WHICH VOUCHER
        if($request->api == 'atm')
        {
            // activtemplate
            return self::get_activtemplate_coupon($user,$coin,$pc);
        }
        elseif($request->api == 'omn')
        {
            // omnilinks
            $key = "uafxja41pvj1btd83jgqzax7n";
            $url = env('API_OMNILINKS')."/wm-coupon";
        }
        elseif($request->api == 'act')
        {
            // activrespon
            $key = "t4ydaq0ed6c2pqi82zje4rit";
            $url = env('API_ACTIVRESPON')."/gen-coupon";
        }
        else
        {
            $ret['api'] = false;
            return response()->json($ret);
        }

 		$data = [
 			'diskon_value'=>$discount,
 			'key'=>$key,
 		];

 		$data_api = json_encode($data);

 		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json'
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result)) 
        {
            return false;
        }

        curl_close($ch);

        $response = json_decode($result,true);

        if(isset($response['coupon']))
        {
            if($response['coupon'] !== 0)
            {
                $svcoin = self::save_coin($user,$coin);
                $response['coin'] = $pc->pricing_format($svcoin);
            }
        }

        if(isset($response['act_coupon']))
        {
            if($response['act_coupon'] !== 0)
            {
                $svcoin = self::save_coin($user,$coin);
                $response['coin'] = $pc->pricing_format($svcoin);
            }
        }

        return $response;
 	}

    private static function get_activtemplate_coupon($user,$coin,$pc)
    {
        if($coin == 2000000)
        {
            $logic = ['type','=',2];
        }
        else
        {
            $logic = ['type','=',1];
        }

        $atm = Atcoupons::where([['is_used',0],$logic])->first();

        if(is_null($atm))
        {
            $response['atm_coupon'] = 0;
        }
        else
        {
            $atcoupon = [
                'user_id'=>$user->id,
                'date_used'=>Carbon::now()->toDateTimeString(),
                'is_used'=>1,
            ];

            try
            {
                Atcoupons::where('id',$atm->id)->update($atcoupon);
                $svcoin = self::save_coin($user,$coin);
                $response['coin'] = $pc->pricing_format($svcoin);
                $response['atm_coupon'] = $atm->code;
            }
            catch(QueryException $e)
            {
                //$e->getMessage();
                $response['atm_coupon'] = 0;
            }
        }

        return response()->json($response);
    }

    /*SAVE AND UPDATE COIN AFTER EXCHANGE WITH VOUCHER*/
    private static function save_coin($user,$coin)
    {
        $user->coin -= $coin;

        try
        {
            $user->save();
            $coin_remain = $user->coin;
        }
        catch(QueryException $e)
        {
            // $e->getMessage();
            $coin_remain = 0;
        }

        return $coin_remain;
    }

/*end class*/
}
