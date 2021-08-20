<?php
namespace App\Helpers;

class Api
{

    public static function send_wa_message($admin_id,$msg,$to)
    {
        // $admin_id --- user id activrespon
        $data = [
            'token'=>'AX2557fd253Topq1A2',
            'admin_id'=>$admin_id,
            'message'=>$msg,
            'to'=>$to,
        ];

        $data_api = json_encode($data);

        $url = 'https://activrespon.com/dashboard/api/celebfans';
        // $url = 'https://192.168.100.49/activrespon/api/celebfans';
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
        // dd($result);
        if (curl_errno($ch) != 0 && empty($result)) 
        {
            return false;
        }

        curl_close($ch);

        $response = json_decode($result,true);
        return $response;
    }

/**/
}