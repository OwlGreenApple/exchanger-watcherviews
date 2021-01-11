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

//GET PACKAGE ADMIN FEE
function getPackageFee($package)
{
    $pckg = [
      getPackage()[0]['package'] => ['fee'=>35,'extra'=>10], //pro
      getPackage()[1]['package'] => ['fee'=>25,'extra'=>20], //pro
      getPackage()[2]['package'] => ['fee'=>15,'extra'=>30], //pro
    ];

    return $pckg[$package];
}

?>