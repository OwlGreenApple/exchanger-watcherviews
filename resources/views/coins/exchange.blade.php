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
                    <div class="error exchange"></div>
                   
                    <div class="col-lg-5 col-md-4 col-sm-12 col-12 px-0 mt-2">

                      <div class="form-group">
                        <label>Link Video</label>
                        <input type="text" class="form-control" name="link_video" />
                        <div class="error link_video"></div>
                      </div>
                      
                      <div class="form-group">
                        <label>Views</label>
                        <input type="text" class="form-control" name="views" />
                        <div class="error views"></div>
                       <!--  <input type="number" min="1" max="999" value="1" name="total_views" />
                        <label>&nbsp;X 1000 Views</label> -->
                      </div>
                      
                      <div class="form-group form-inline">
                        <label class="mr-2">Drip-Feed</label>
                        <input type="checkbox" name="drip" />
                      </div>
                      
                      <div class="form-group runs">
                        <label>Runs</label>
                        <input type="text" class="form-control" name="runs" value="1" />
                        <div class="error errruns"></div>
                      </div>

                      <div class="form-group form-inline">
                        <label class="mr-2">Total Views</label>
                        <div id="total_views" class="form-control"></div>
                      </div>
                    </div>

                    <div class="mt-2 mb-2 input-group col-lg-7">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Total Coins Charge :</span>
                      </div> 
                      <b class="form-control" id="total">0</b>
                    </div>

                    <button type="button" id="purchase" type="submit" class="btn btn-primary">Purchase</button>
                  </form>

              </div>
          </div>
      </div>
  </div>

  <div class="row justify-content-center mt-3 bg-white py-2">
    <h5><b>Exchange Transaction</b></h5>
    <div class="col-lg-12 table-responsive">
      <table id="exchanged_coins" class="table table-striped table-bordered">
        <thead align="center">
          <th>Created</th>
          <th>Duration</th>
          <th>Coins Rate</th>
          <th>Youtube Link</th>
          <th>Views</th>
          <th>Drip</th>
          <th>Total Coins</th>
          <th>Total Views</th>
         <!--  <th>Status</th> -->
        </thead>
        <tbody id="content"></tbody>
      </table>
  </div>

  </div>

</div>

<!-- Modal allocate coins -->
<div class="modal fade" id="allocate" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Allocate Views
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

        <form>
         <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="allocate" checked/>Direct
            </label>
         </div>
         <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="allocate">Drip
            </label>
         </div>

      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Close
        </button>
      </div>
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

    display_table();
    calculate_coins();
    get_total_coins();
    exchange_coins();
    check_drip();
    calculate_drip();
  });

  function check_drip()
  {
    $(".runs").hide();
    $("input[name='drip']").click(function(){
      var views = $("input[name='views']").val();
      drip(views);
    });
  }

  function drip(views)
  {
    var drip = $("input[name='drip']").is(':checked');
    var runs = 1;
    if(drip == true)
    {
      $(".runs").show();
      $("input[name='drip']").val(1);
    }
    else
    {
      $(".runs").hide();
      $("input[name='runs']").val(runs);
      $("input[name='drip']").val(0);
    }
    drip_formula(runs,views)
  }

  function calculate_drip()
  {
    $("input[name='runs']").on("keypress keyup",function(){
        var val = $(this).val();
        var views = $("input[name='views']").val();
        drip_formula(val,views);
    });
  }

  function drip_formula(runs,views)
  {
     calculate_coins(runs);
     var calculate = runs * views;
     $("#total_views").html(calculate);
  }

  function get_total_coins()
  {
    $("input[name='views']").on("keypress keyup",function(){
        var runs = $("input[name='runs']").val();
        drip_formula(runs,$(this).val())
        $(this).val(formatting($(this).val()));
    });

    $("input[name='exchange']").change(function(){
        var runs = $("input[name='runs']").val();
        var views = $("input[name='views']").val();
        drip_formula(runs,views)
    });
  }

  function formatting(num)
  {
      // console.log(num);
      num = num.toString().replace(/(\.)/g,"");
      num = parseInt(num);
      var max_views = 10000;

      if(num > max_views)
      {
        return formatNumber(max_views);
      }
      else
      {
        return formatNumber(num);
      }
  }

  function calculate_coins(runs)
  {
    var total_coins;
    var total_views = $("input[name='views']").val();
    var coins_price = $("input[name='exchange']:checked").attr('data-coins');
    coins_price = coins_price/1000;

    var drip = $("input[name='drip']").is(':checked');
    if(drip == true)
    {
      total_coins = total_views * coins_price * runs;

    }
    else
    {
      total_coins = total_views * coins_price;
    }

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

  function display_table()
  { 
    $.ajax({
      type : 'GET',
      url : "{{ url('exchange-table') }}",
      dataType: 'html',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
      
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        $("#content").html(result);
      },
      error : function(xhr)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        console.log(xhr.responseText);
      }
    });
  }

  function exchange_coins()
  {
    $("#purchase").click(function(){
      var data = $("#submit_exchange").serializeArray();
      purchase(data);
    });
  }

  function purchase(data)
  {
    $.ajax({
       headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },

      type : 'POST',
      url : "{{ url('exchange-submit-coins') }}",
      data : data,
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
          $(".error").show();
          $(".exchange").html(result.exchange);
          $(".link_video").html(result.link_video);
          $(".views").html(result.views);
          $(".errruns").html(result.runs);
        }
        else
        {
           $(".error").hide();
           $("#status_msg").html('<div class="alert alert-success">Your coins has been exchanged successfully.</div>')
            $("#current_coins").html(formatNumber(result.credit));
            $("input").val("");
            calculate_coins();
            display_table();
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
