<?php

//GET PACKAGE PRICING
function getPackage()
{
    $package = array(
      0 => ['package'=>'pro','price'=>295000],
      1 => ['package'=>'master','price'=>395000],
      2 => ['package'=>'super','price'=>495000]
    );

    return $package;
}

//ID PACKAGE VALIDATION
function validationPackage($idpackage)
{
    $packageid = ["0","1","2"];
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
function getPackageRate($package)
{
    $pckg = [
      'free' => 10000, //free
      getPackage()[0]['package'] => 9000, //pro
      getPackage()[1]['package'] => 8250, //master
      getPackage()[2]['package'] => 7250, //super
    ];

    return $pckg[$package];
}

?>