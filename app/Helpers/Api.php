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

        // note : this using celebfans page link, due same logic
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

    public static function connect_watcherviews($email,$password)
    {
        $data = [
            'token'=>'AX2557fd253Topq1A2',
            'email'=>$email,
            'password'=>$password,
        ];

        $data_api = json_encode($data);

        // $url = 'https://watcherviews.com/dashboard/exchanger-coin';
        $url = 'https://192.168.100.49/watcherviews/connect-exchanger';
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

    public function get_total_coin($wt_id)
    {
        $data = [
            'token'=>'AX2557fd253Topq1A2',
            'id'=>$wt_id
        ];

        $data_api = json_encode($data);

        // $url = 'https://watcherviews.com/dashboard/exchanger-coin';
        $url = 'https://192.168.100.49/watcherviews/exchanger-total-coin';
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

    public static function transaction_wt_coin($coin,$user_id,$method)
    {
        $data = [
            'token'=>'AX2557fd253Topq1A2',
            'amount'=>$coin,
            'id'=>$user_id,
        ];

        if($method == 1)
        {
            $target = 'exchanger-coin';
        }
        else
        {
            $target = 'exchanger-send';
        }

        $data_api = json_encode($data);

        // $url = 'https://watcherviews.com/dashboard/'.$target;
        $url = 'https://192.168.100.49/watcherviews/'.$target;
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