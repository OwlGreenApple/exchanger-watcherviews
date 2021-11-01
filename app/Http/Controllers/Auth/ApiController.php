<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class ApiController extends RegisterController
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
 			$this->create($data);
 		}
 		else
 		{
 			// REGISTERED USER BUT DIDN'T GET PROMOTE
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
 		if($request->api == 'omn')
 		{
 			// omnilinks
 			$key = "uafxja41pvj1btd83jgqzax7n";
 			$url = env('API_OMNILINKS')."/wm-coupon";
 		}
 		else
 		{
 			// activrespon
 			$key = "t4ydaq0ed6c2pqi82zje4rit";
 			$url = env('API_ACTIVRESPON')."/gen-coupon";
 		}

 		if($request->diskon_value == 2)
 		{
 			$discount = 200000;
 		}
 		else
 		{
 			$discount = 100000;
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
        return $response;
 	}

/*end class*/
}
