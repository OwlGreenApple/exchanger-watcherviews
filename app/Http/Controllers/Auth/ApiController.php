<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class ApiController extends RegisterController
{

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

/*end class*/
}
