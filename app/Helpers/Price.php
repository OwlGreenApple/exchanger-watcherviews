<?php
namespace App\Helpers;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class Price
{

	//LIST OF PRICE,PACKAGE AND DESCRIPTION
	/*
		sell = n per day
		profit = n%
	*/
    public function get_price()
    {
        $price = [
            ['package'=>'free','price'=>0,'max_coin'=>200000,'fee'=>15000,'sell'=>1,'profit'=>0],
            ['package'=>'starter','price'=>100000,'max_coin'=>500000,'fee'=>10000,'sell'=>2,'profit'=>0.5],
            ['package'=>'doubler','price'=>200000,'max_coin'=>1000000,'fee'=>10000,'sell'=>3,'profit'=>0.5],
            ['package'=>'tripler','price'=>300000,'max_coin'=>2000000,'fee'=>10000,'sell'=>4,'profit'=>1],
            ['package'=>'quadrupler','price'=>400000,'max_coin'=>3000000,'fee'=>10000,'sell'=>5,'profit'=>1.5]
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

    //TRANSACTION LOGIC
    public static function transaction(array $data)
    {
    	$tr = new Transaction;
    	$tr->user_id = Auth::id();
    	$tr->no = $data['no'];
    	$tr->type = $data['type'];
    	$tr->amount = $data['amount'];

    	try
    	{
    		$tr->save();
    		$response['err'] = 0;
    	}
    	catch(QueryException $e)
    	{
    		// $response['err'] = $e->getMessage();
    		$response['err'] = 1;
    	}

    	return $response ;
    }

/*end of class*/
}