@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Referral Programs</b></h2>  
    
      <hr>
    </div>
  </div>

  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-body text-center">
                <div id="message"><!--  --></div>
                <div class="input-group mb-3">
                  <input id="ref_link" class="form-control" readonly="readonly" value="{{ $referral_link }}" />
                  <div class="input-group-append">
                    <button class="btn btn-primary btn-sm btn-copy">Copy Link</button>
                  </div>
                </div>
                <button id="generate_link" class="btn btn-primary btn-sm">Generate Link</button>
              </div>
          </div>
      </div>
  </div>

  <div class="col-lg-8 justify-content-center mt-3 mx-auto bg-white py-2">
    <h5 class="text-center"><b>Referral List</b></h5>
    <div class="table-responsive">
      <table id="referral_list" class="table table-striped table-bordered">
        <thead align="center">
          <th>User Name</th>
          <th>Joined</th>
         <!--  <th>Status</th> -->
        </thead>
        <tbody id="content"></tbody>
      </table>
  </div>

  </div>
<!--  -->
</div>

<!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Copy Link
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        You have copied the link!
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function()
  {
    copyLink();
    generate_ref_link();
   /* let table = $("#exchanged_coins").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : [],
      "destroy" : true
    });

    display_table();
    calculate_coins();
    get_total_coins();
    exchange_coins();
    calculate_drip();
    drip_formula(1,100);*/
  });

  // GLOBAL VARIABLE
  const max_value = 10000;

  function generate_ref_link()
  { 
    $("#generate_link").click(function(){
      $.ajax({
        type : 'GET',
        url : "{{ url('referral-link') }}",
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
        
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          
          if(result.msg == 0)
          {
            $("#ref_link").val(result.link);
            $("#message").html("<div class='alert alert-primary'>Your referral link has been generated, please copy link on below.</div>")
          }
          else
          {
            $("#message").html("<div class='alert alert-danger'>Currently our server is too busy, please try again later.</div>")
          }
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    });
  }

  function copyLink(){
    $( "body" ).on("click",".btn-copy",function(e) 
    {
      e.preventDefault();
      e.stopPropagation();

      var link = $("#ref_link").val();

      var tempInput = document.createElement("input");
      tempInput.style = "position: absolute; left: -1000px; top: -1000px";
      tempInput.value = link;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      $('#copy-link').modal('show');
    });
  }

  /////////////////////////////////

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
        var runs = formatted_runs($("input[name='runs']").val());
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
    var drip = $("input[name='drip']").is(':checked');
    var runs = $("input[name='runs']").val();

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
    else if(views < 100)
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
