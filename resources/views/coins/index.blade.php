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
                 <span id="status_msg"><!-- message --></span>
                 <div><input id="total_coins" class="form-control w-50" /></div>
                 <small>Coins must be multiplied of 100,000</small>
                 <div id="rate" data-price="{!! getPackageRate($user->membership) !!}">Price : Rp {!! str_replace(",",".",number_format(getPackageRate($user->membership))) !!} /100.000 coins</div>
                 <div>Total : Rp <span id="total">0</span></div>
                 <div id="purchase" class="btn btn-primary">Purchase</div>
              </div>
          </div>
      </div>
  </div>

</div>

<script type="text/javascript">

  $(document).ready(function(){
    check_coins();
    buy_coins();
  });

  //GLOBAL VARIABLE
  var min = 500000; //coins

  /*DELAY ON KEYUP*/
  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }  

  function buy_coins()
  {
    $("#purchase").click(function(){
      var coins = $("#total_coins").val();

      if(coins_validity(coins) == true)
      {
         coins = coins.toString().replace(/(\.)/g,"");
         coins = parseInt(coins);
         purchase(coins);
      }
      
    });
  }

  function purchase(coins)
  {
    $.ajax({
       headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },

      type : 'POST',
      url : "{{ url('purchase-coins') }}",
      data : {"coins":coins},
      dataType: 'json',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
      
        if(result.msg == 1)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#status_msg").html('<div class="alert alert-danger">Sorry, our server is too busy, please try again later.</div>')
        }
        else if(result.msg == 2)
        {
          //serverside validation
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#status_msg").html('<div class="alert alert-danger">'+result.message+'</div>')
        }
        else
        {
          location.href="{{ url('thankyou') }}";
        }
      },
      error : function(xhr)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        console.log(xhr.responseText);
      }
    });
  }

  function check_coins()
  { 
    var min_price = min/100000;
    var price = "{{ getPackageRate($user->membership) }}";
    price = parseInt(price);
    var total_price = price * min_price; 
    $("#total_coins").val(formatNumber(min));
    $("#total").html(formatNumber(total_price));

    //PROCESS
    $("#total_coins").on('keyup keypress', function()
    {
      // var n = parseInt($(this).val().replace(/\D/g,''),10);
      var n = $(this).val().toString().replace(/(\.)/g,"");
      var len = n.length;
      var cvt = parseInt(n);
  
      if(len >= 9)
      {
        var previous_price = n;
        var digit = [
          previous_price.charAt(0),
          previous_price.charAt(1),
          previous_price.charAt(2),
          previous_price.charAt(3),
        ]
        
        var revert = digit[0]+digit[1]+digit[2]+digit[3]+'00000';
        revert = parseInt(revert);

        // $(this).val(revert.toLocaleString());
        $(this).val(formatNumber(revert));
        calculate_coins(revert,price);
      }
      else
      {
        $(this).val(formatNumber(cvt));
        calculate_coins(cvt,price);
      }
    });
  }

  function calculate_coins(coins,price)
  {
    var coin = coins;
    var sum = coin/100000;
    var total = sum * price;
    
    // total = total.toLocaleString('fullwide', {useGrouping:false});
    $("#price").attr('data-price',total);
    $("#total").html(formatNumber(total));
  }

  function check_min_coin(min_coins)
  {
    min_coins = min_coins.toString().replace(/(\.)/g,"");
    min_coins = parseInt(min_coins);

    if(min_coins < min) { 
      return false;
    }
    else
    {
      return true;
    }
  } 

  function coins_validity(coins)
  {
     if(coins === undefined)
     {
        return false;
     }

     if(check_min_coin(coins) == false)
     {
        alert('Minimum coins for purchasing is 500,000');
        return false;
     }

     coins = coins.toString().replace(/(\.)/g,"");
     var coin = parseInt(coins);
     var total_coin = coin%100000;
      
     if(total_coin > 0)
     {
       alert('The amount of coins must multiplied of 100,000');
       return false;
     }
     else
     {
       return true;
     }
  }

  function formatNumber(num) 
  {
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
