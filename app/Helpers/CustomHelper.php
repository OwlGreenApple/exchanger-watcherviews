<?php

//GET PACKAGE PRICING
function getPackage()
{
    $pro_coins = 1000000;
    $master_coins = 2000000;
    $super_coins = 3000000;

    $package = array(
      1 => ['package'=>'pro','price'=>295000,'bc'=>$pro_coins,'bonus'=>'Bonus '.str_replace(",",".",number_format($pro_coins)).' coins','disc'=>'Disc 10% untuk setiap pembelian Coin'],
      2 => ['package'=>'master','price'=>395000,'bc'=>$master_coins,'bonus'=>'Bonus '.str_replace(",",".",number_format($master_coins)).' coins','disc'=>'Disc 18% untuk setiap pembelian Coin'],
      3 => ['package'=>'super','price'=>495000,'bc'=>$super_coins,'bonus'=>'Bonus '.str_replace(",",".",number_format($super_coins)).' coins','disc'=>'Disc 28% untuk setiap pembelian Coin']
    );

    return $package;
}

//ID PACKAGE VALIDATION
function validationPackage($idpackage)
{
    $packageid = ["1","2","3"];
    if(in_array($idpackage, $packageid))
    {
      return true;
    }
    else
    {
      return false;
    }    
}

//GET CELEBFANS RATES (PRICE PER 100.000 COINS)
function getPackageRate($package = null)
{
    if(empty($package) || $package == null || $package == "")
    {
      $package = 'free';
    }

    $pckg = [
      'free' => 10000, //free
      getPackage()[1]['package'] => 9000, //pro
      getPackage()[2]['package'] => 8250, //master
      getPackage()[3]['package'] => 7250, //super
    ];

    return $pckg[$package];
}

//EXCHANGE COINS RATE VIEWS/1000
/*
  use on : CoinsController::exchange
  use on : CoinsController::submit_exchange
  use on : CoinsController::coin_get
*/
function getExchangeRate($x)
{
    $rate = [
      1=>['duration'=>30,'coins'=>400000],
      2=>['duration'=>45,'coins'=>460000],
      3=>['duration'=>60,'coins'=>520000],
      4=>['duration'=>90,'coins'=>640000],
      5=>['duration'=>120,'coins'=>760000],
      6=>['duration'=>150,'coins'=>880000],
      7=>['duration'=>180,'coins'=>1000000]
    ];

    return $rate[$x];
}

?>