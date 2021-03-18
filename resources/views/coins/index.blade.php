@extends('layouts.app')

@section('content')

<div class="bg-custom">
  <div class="container">
    <div class="col-md-12 py-4">
      <h2 class="mb-0"><b class="mr-2">Beli Koin</b><i class="fas fa-coins"></i></h2>  
    </div>
  </div>
</div>

<hr>

<!-- banner image -->
<div class="container text-center mb-2">
  <img src="{{ asset('assets/img/banner.jpg') }}" class="image_banner" />
</div>

<div class="container mb-5 main-cont">
  <div class="row justify-content-center">
      <div class="col-md-9">
          <div class="card py-4">
              <div class="card-body col-lg-7 mx-auto px-0 py-0">
                 <span id="status_msg"><!-- message --></span>
                 <label><b>Jumlah Koin</b> <i class="fas fa-coins"></i></label>
                 <div><input id="total_coins" class="form-control" /></div>
                 <small>Koin harus kelipatan 100,000</small>
                 <div class="py-1" id="rate" data-price="{!! getPackageRate($user->membership) !!}">Rate <i class="fas fa-exchange-alt"></i> : <b>Rp {!! str_replace(",",".",number_format(getPackageRate($user->membership))) !!} /100.000 coins</b></div>

                 <!-- div class="mt-2 mb-2 input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-success text-white">Total Rp :</span>
                    </div> 
                    <b class="form-control" id="total">0</b>
                  </div> -->
                 <div id="total" class="form-control bg-success text-white text-center mb-2">0</div>
                 <div id="purchase" class="btn btn-dark w-100">Beli</div>
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
          $("#status_msg").html(alert_wrapper('Maaf server kami sedang sibuk, coba lagi nanti.'));
        }
        else if(result.msg == 2)
        {
          //serverside validation
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#status_msg").html(alert_wrapper(result.message));
        }
        else
        {
          location.href="{{ url('thank_you') }}";
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
    $("#total").html('Rp '+formatNumber(total_price));

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
    $("#total").html('Rp '+formatNumber(total));
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
        $("#status_msg").html(alert_wrapper('Minimal pembelian koin adalah sebesar 500.000'));
        return false;
     }

     coins = coins.toString().replace(/(\.)/g,"");
     var coin = parseInt(coins);
     var total_coin = coin%100000;
      
     if(total_coin > 0)
     {
       $("#status_msg").html(alert_wrapper('Jumlah koin harus kelipatan 100,000'));
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
