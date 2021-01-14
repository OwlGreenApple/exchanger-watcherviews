<?php

//GET PACKAGE PRICING
function getPackage()
{
    $package = array(
      1 => ['package'=>'pro','price'=>295000],
      2 => ['package'=>'master','price'=>395000],
      3 => ['package'=>'super','price'=>495000]
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

?>