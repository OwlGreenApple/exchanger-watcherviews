<?php
namespace App\Helpers;
use App\Models\Transaction;
use App\Models\Kurs;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class Price
{

	//LIST OF PRICE,PACKAGE AND DESCRIPTION
	/*
		sell = n per day
		profit = fee = n%
		max_sell = Rp
	*/
    public function get_price()
    {
        $price = [
            ['package'=>'free','price'=>0,'max_sell'=>30000,'max_trans'=>10000,'fee'=>15,'sell'=>1,'profit'=>0],
            ['package'=>'starter','price'=>100000,'max_sell'=>500000,'max_trans'=>50000,'fee'=>10,'sell'=>2,'profit'=>0.5],
            ['package'=>'doubler','price'=>200000,'max_sell'=>2000000,'max_trans'=>100000,'fee'=>10,'sell'=>3,'profit'=>0.5],
            ['package'=>'tripler','price'=>350000,'max_sell'=>3500000,'max_trans'=>150000,'fee'=>10,'sell'=>4,'profit'=>1],
            ['package'=>'quadrupler','price'=>500000,'max_sell'=>5000000,'max_trans'=>200000,'fee'=>10,'sell'=>5,'profit'=>1.5]
        ];

        return $price;
    }

    public function check_type($package)
    {
        $pack = [
            $this->get_price()[0]['package'] => $this->get_price()[0],
            $this->get_price()[1]['package'] => $this->get_price()[1],
            $this->get_price()[2]['package'] => $this->get_price()[2],
            $this->get_price()[3]['package'] => $this->get_price()[3],
            $this->get_price()[4]['package'] => $this->get_price()[4]
        ];

        if(isset($pack[$package]))
        {
            return $pack[$package];
        }
        else
        {
            return false;
        } 
    }

    //FORMATTING NUMBER WITH . SIGN
    public function pricing_format($price)
    {
        return str_replace(",",".",number_format($price));
    }

    public static function convert_number($num)
    {
        $coin = str_replace(".","",$num);
        $coin = (int)$coin;
        return $coin;
    }

    //NOTIFICATION SELLER LOGIC
    public static function events()
    {
    	$ev = Event::where([['user_id',Auth::id()],['is_read',0]])->get();
    	$total_ev = $ev->count();
        $data = [
            'total'=>$total_ev,
            'data'=>$ev
        ];
    	return $data;
    }

    public static function get_rate()
    {
        $rate = Kurs::select('kurs')->orderBy('id','desc')->first();
        return $rate->kurs;
    }

    public static function total_dispute()
    {
        $tr = Transaction::where('status',4)->orderBy('created_at','desc')->get();
        $total_dispute = $tr->count();
        $new_dispute = 0;
        $today = Carbon::today();

        // TO CHECK IF DISPUTE IS NEW OR OLD
        if($total_dispute > 0)
        {
            foreach($tr as $row)
            {
                if($today->lte(Carbon::parse($row->updated_at)))
                {
                    $new_dispute = 1;
                }
            }
        }

        $data = [
            'total'=>$total_dispute,
            'new'=>$new_dispute,
        ];

        return $data;
    }

    // EXPLODE DATA FROM PAYMENT METHOD
    public static function explode_payment($data)
    {
        if($data !== null)
        {
          $result = explode("|",$data);
        }
        else
        {
          $result[0] = $result[1] = $result[2] = $data;
        }

        return $result;
    }

    public static function buyer_limit_day($total_transaction)
    {
        if($total_transaction <= 10)
        {
            return 20000;
        }
        elseif($total_transaction <= 30)
        {
            return 100000;
        }
        else
        {
            return 200000;
        }
    }

    public static function check_blocked_user($blocked_buyer)
    {
        $blocked_list = explode("|",$blocked_buyer);
        return in_array(Auth::id(), $blocked_list);
    }

/*end of class*/
}