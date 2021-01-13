@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Buy Coins</b></h2>  
    
      <hr>
    </div>
  </div>

  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-body">
                 <div><input id="total_coins" placeholder="min 500,000 coins" class="form-control w-50" /></div>
                 <div id="rate" data-price="{!! getPackageRate('free') !!}">Price : Rp {!! str_replace(",",".",number_format(getPackageRate('free'))) !!} /100.000 coins</div>
                 <div id="">Total : Rp <span id="total">0</span></div>
                 <div class="btn btn-primary">Purchase</div>
              </div>
          </div>
      </div>
  </div>

</div>

<script type="text/javascript">

  $(document).ready(function(){
    check_coins();
    check_multiply();
  });

  /*DELAY ON KEYUP*/
/*  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }  */

  function check_coins()
  {
    $("#total_coins").on('keyup', function(){
      var n = parseInt($(this).val().replace(/\D/g,''),10);
      if(isNaN(n) == true)
      {
        $(this).val(0);
      }
      else
      {
       $(this).val(n.toLocaleString());
      }
    });
  }

  function check_min_coin()
  {
    setTimeout(function(){
        var min_coins = $(this).val();
        console.log(min_coins);
       /* min_coins = min_coins.replace(".","");
        min_coins = parseInt(min_coins);

        if(min_coins < 500000) { 
          alert('minimum to buy is 500.000');
          $(this).val('');
          return false;
        }*/
      },900);
  } 

  function check_multiply(coins)
  {
     var price = "{{ getPackageRate('free') }}";
     price = parseInt(price);
     var coin = parseInt(coins);
     var total_coin = coin%100000;
      
     if(total_coin > 0){
      alert('Should multiple of 100.000');
      return false;
     }

     var sum = coin/100000;
     var total = sum * price;
     $("#total").html(total);
  }

  function formatNumber(num) 
  {
    num = parseInt(num);
    if(isNaN(num) == true)
    {
       return '';
    }
    else
    {
       return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }
  }
</script>

@endsection
