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
                        <input type="text" class="form-control" name="views" value="100" />
                        <div class="error views"></div>
                       <!--  <input type="number" min="1" max="999" value="1" name="total_views" />
                        <label>&nbsp;X 1000 Views</label> -->
                      </div>
                      
                      <!-- if(Auth::user()->membership == 'super') -->
                      <div class="form-group form-inline">
                        <label class="mr-2">Drip-Views <i class="ml-2 fa fa-question-circle question" aria-hidden="true"></i></label>
                        <input type="checkbox" name="drip" />
                      </div>
                      
                      <div class="form-group runs">
                        <label>Runs</label>
                        <input type="text" class="form-control" name="runs" value="1" />
                        <div class="error errruns"></div>
                      </div>
                      <!-- endif -->

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
    <div class="col-lg-12 table-responsive" id="content"><!-- datatable --></div>
  </div>

</div>

<script type="text/javascript">

  $(document).ready(function()
  {
    display_table();
    calculate_coins();
    get_total_coins();
    exchange_coins();
    check_drip();
    calculate_drip();
    drip_formula(1,100);
    tooltips();
  });

  // GLOBAL VARIABLE
  const max_value = 10000;

  function tooltips()
  {
    $('.question').tooltip({
      'html':true,
      'title': "Fitur Drip Views adalah Fitur untuk membagi views beberapa kali pada video yang sama dalam waktu random,sehingga penambahan views video terlihat lebih organik <br/><br/> Contoh : Video A : 200 Views untuk 11x = Total 2200 views."
    });
  }

  function check_drip()
  {
    $(".runs").hide();
    $("input[name='drip']").click(function(){
      var views = $("input[name='views']").val();
      drip_display(views);
    });
  }

  function drip_display(views)
  {
    var runs = 1;
    var drip = $("input[name='drip']").is(':checked');
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
    drip_formula(runs,views);
  }

  function calculate_drip()
  {
    $("input[name='runs']").on("keypress keyup",function(){
        var val = $(this).val();
        var views = $("input[name='views']").val();
        drip_formula(val,views);
        $(this).val(formatting(val));
    });
  }

  function drip_formula(runs,views)
  {
     views = parseInt(views.toString().replace(/(\.)/g,""));
     var drip = $("input[name='drip']").is(':checked');

     if(drip == true){
        runs = formatted_runs(runs);
     }
     else
     {
        runs = 1;
     }
     calculate_coins(runs);

     if(views > max_value)
     {
       views = max_value;
     }

     var calculate = runs * views;
     $("#total_views").html(formatNumber(calculate));
  }

  function calculate_coins(runs)
  {
    if(runs === undefined){
      runs = 1;
    }

    var total_coins;
    var total_views = $("input[name='views']").val();
    var coins_price = $("input[name='exchange']:checked").attr('data-coins');
    coins_price = coins_price/1000;
    total_views = total_views.toString().replace(/(\.)/g,"");
    total_views = parseInt(total_views);

    if(total_views > max_value){
      total_views = max_value;
    }

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

  function formatted_runs(runs)
  {
    runs = formatting(runs);
    runs = runs.toString().replace(/(\.)/g,"");
    runs = parseInt(runs);
    return runs;
  }

  function get_total_coins()
  {
    $("input[name='views']").on("keypress keyup",function(){
        var run_value =$("input[name='runs']").val();
        if(run_value === undefined)
        {
          run_value = 1;
        }
        var runs = formatted_runs(run_value);
        drip_formula(runs,$(this).val())
        $(this).val(formatting($(this).val()));
    });

    $("input[name='exchange']").change(function(){
        var run_value =$("input[name='runs']").val();
        if(run_value === undefined)
        {
          run_value = 1;
        }
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

      if(num > max_value)
      {
        return formatNumber(max_value);
      }
      else
      {
        return formatNumber(num);
      }
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

      if(validator() == true)
      {
        purchase(data);
      }
    });
  }

  function validator()
  {
    var link_video = $("input[name='link_video']").val();
    var views = $("input[name='views']").val();
    var runs = $("input[name='runs']").val();
    var drip = $("input[name='drip']").is(':checked');

    if(link_video.toString().length == 0)
    {
      alert('Field youtube link cannot be empty');
      return false;
    }
    else if(views.toString().length == 0)
    {
      alert('Field views cannot be empty');
      return false;
    }
    else if(formatted_runs(views) < 100)
    {
      alert('Field views at least 100');
      return false;
    }
    else if(drip == true && runs < 1)
    {
      alert('Field runs at least 1');
      return false;
    }
    else if(drip == true && runs.toString().length == 0)
    {
      alert('Field runs cannot be empty');
      return false;
    }
    else
    {
      return true;
    }
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
            $("input[name='link_video']").val("");
            $("input[name='views']").val(100);
            $("input[name='runs']").val(1);
            $("#total_views").html(100);
            calculate_coins(1);
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
