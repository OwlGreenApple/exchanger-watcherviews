@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Exchange Coins</b></h2>  
    
      <hr>
    </div>
  </div>

  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-body">
                 <span id="status_msg"><!-- message --></span>
                  <form id="submit_exchange">

                    <!-- display exchange rate -->
                    @for($x=1; $x<=7; $x++)
                    <div class="form-check">
                      @if($x == 1) 
                      <label class="form-check-label">
                        <input type="radio" data-coins="{!! getExchangeRate($x)['coins'] !!}" class="form-check-input" name="exchange" value="{{ $x }}" checked/>{!! getExchangeRate($x)['duration'] !!} sec = {!! number_format(getExchangeRate($x)['coins']) !!} coins / 1000 views
                      </label>
                      @else
                      <label class="form-check-label">
                        <input type="radio" data-coins="{!! getExchangeRate($x)['coins'] !!}" class="form-check-input" name="exchange" value="{{ $x }}"/>{!! getExchangeRate($x)['duration'] !!} sec = {!! number_format(getExchangeRate($x)['coins']) !!} coins / 1000 views
                      </label>
                      @endif
                    </div>
                    @endfor

                    <div class="mt-3 mb-2">Your Coins : <b>{{ number_format($user->credits) }}</b></div>
                   
                    <div class="col-lg-3 col-md-4 col-sm-12 col-12 px-0 mt-2 form-inline">
                      <div class="form-group">
                        <input type="number" min="1" max="999" value="1" name="total_views" />
                        <label>&nbsp;X 1000 Views</label>
                      </div>
                     
                    </div>
                    <div class="mt-2 mb-2">Exchange Coins : <b id="total">0</b></div>
                    <button id="purchase" type="submit" class="btn btn-primary">Purchase</button>
                  </form>

              </div>
          </div>
      </div>
  </div>

  <div class="row justify-content-center mt-3 bg-white py-2">
    <h5><b>Exchange Transaction</b></h5>
    <div class="col-lg-12 table-responsive">
      <table id="exchanged_coins" class="table">
        <thead align="center">
          <th>Num</th>
          <th>Duration</th>
          <th>Coins</th>
          <th>Views</th>
          <th>Allocate</th>
        </thead>
        <tbody id="content"></tbody>
      </table>
  </div>

  </div>

</div>

<script type="text/javascript">

  $(document).ready(function()
  {
    let table = $("#exchanged_coins").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : [],
      "destroy" : true
    });
    // format_coins();
    calculate_coins();
    get_total_coins();
    // buy_coins();
  });

  function get_total_coins()
  {
    $("input[name='total_views']").on("mouseup keyup",function(){
        calculate_coins();
    });

    $("input[name='exchange']").change(function(){
        calculate_coins();
    });
  }

  function calculate_coins()
  {
    var total_views = $("input[name='total_views']").val();
    var coins_price = $("input[name='exchange']:checked").attr('data-coins');
    var total_coins = total_views * coins_price;
    $("#total").html(formatNumber(total_coins));
  } 

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

  function format_coins()
  { 
    var min = 1;
    var total_coins = $("input[name='exchange']:checked").attr('data-coins');
    $("#total_views").val(min.toLocaleString());
    $("#total").html(formatNumber(total_coins));

    $("#total_views").keyup(function(){
      var val = $(this).val();
      $(this).val(formatNumber(val));
    });
  }

  function buy_coins()
  {
    $("#purchase").click(function(){
      var coins = $("#total_coins").val();

      if(coins_validity(coins) == true)
      {
         coins = coins.toString().replace(/(\,)/g,"");
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
      
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');

        if(result.msg == 1)
        {
          $("#status_msg").html('<div class="alert alert-danger">Sorry, our server is too busy, please try again later.</div>')
        }
        else if(result.msg == 2)
        {
          //serverside validation
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
