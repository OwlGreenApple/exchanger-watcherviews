@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{asset('assets/css/checkout.css')}}">

<div class="container" style="margin-top:50px; margin-bottom:100px">
  <div class="row justify-content-center">
    <div class="col-md-8 col-12">
      <div class="card-custom">
        <div class="card cardpad">

          <!-- FORM -->
						<form id="submit_checkout">
              <h2 class="Daftar-Disini">{{ $lang::get('custom.pilih_paket') }}</h2>
							
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <label class="text" for="formGroupExampleInput">{{ $lang::get('custom.paket') }} :</label>
                  <select class="form-control text-capitalize" id="select-auto-manage">
                      @foreach($api->get_price() as $index=>$row)
                        @if($row['price'] > 0) 
                           @if($index == $page)
                            <option data-price="{{ $row['price'] }}" value="{{ $row['package'] }}" selected="selected">
                              {{$row['package']}} - IDR {{ $api->pricing_format($row['price']) }} {{ $lang::get('custom.package_terms') }} - {{ $api->pricing_format($row['max_coin']) }} {{$lang::get('custom.max_coin')}} - {{ $api->pricing_format($row['fee']) }} {{$lang::get('custom.post_fee')}} - {{$row['sell']}} {{$lang::get('custom.sell')}}
                            </option>
                            @else
                            <option data-price="{{ $row['price'] }}" value="{{ $row['package'] }}">
                              {{$row['package']}} - IDR {{ $api->pricing_format($row['price']) }} {{ $lang::get('custom.package_terms') }} - {{ $api->pricing_format($row['max_coin']) }} {{$lang::get('custom.max_coin')}} - {{ $api->pricing_format($row['fee']) }} {{$lang::get('custom.post_fee')}} - {{$row['sell']}} {{$lang::get('custom.sell')}}
                            </option>
                            @endif
                        @endif
                      @endforeach
                  </select>
                  <div class="error price"><!-- error --></div>
                  <div class="error package"><!-- error --></div>
                  <div class="error desc"><!-- error --></div>
                  <div class="error type"><!-- error --></div>
                </div>
                <!--  -->
              </div>

             <!--  if(is_coupon == false)
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Coupon code (optional):
                  </label>

                  <input type="text" class="form-control form-control-lg" name="kupon" id="kupon" placeholder="Kode Kupon Anda" style="width:100%">  
                  <button type="button" class="btn btn-primary btn-kupon  form-control-lg col-md-3 col-sm-12 col-xs-12 mt-3">
                    Apply
                  </button>  
                </div>
              </div>
              endif;
 -->
              <div class="form-group mb-1">
                <div class="col-md-12 col-12">
                  <div id="pesan"><!-- notification --></div>
                </div>
              </div>
             
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Total: 
                  </label>
                  <div class="col-md-12 pl-0">
                    <span class="total" style="font-size:18px"></span>
                  </div>
                  <!-- <label class="mt-2">
                    *Your upgrade will be activated as soon as payment confirmed
                  </label>  -->
                </div>
              </div>

              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required/>
                  <label for="agree-term" class="label-agree-term text">{{ $lang::get('custom.agreement') }}<a href="{{ env('APP_URL') }}terms-of-services/" class="term-service" target="_blank">{{ $lang::get('custom.terms') }}</a></label>
                </div>
              </div>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="submit" name="submit" id="submit" class="col-md-12 col-12 btn btn-primary bsub btn-block" value="{{ $lang::get('custom.proceed') }}"/>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function(){
    setPricing();
    // check_kupon();
    manageSelectPackage();
    checkout();
    change_qty();
    // applyCoupon();
    // setUpgradeOption();
  });

  function checkout()
  {
    $("#submit_checkout").submit(function(e){
      e.preventDefault();
      submit_payment();
    });
  }

  function submit_payment()
  {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: "{{url('submit_payment')}}",
      data: {
        package :  $("#select-auto-manage").find("option:selected").val()
      },
      dataType: 'json',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result)
      {
        if(result.status == 1)
        {
          location.href="{{url('summary')}}";
        }
        else if(result.status == 2)
        {
          location.href="{{url('thankyou')}}";
        }
        else
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.status == 0 && result.msg == undefined)
          {
            $(".alert-danger").hide();
            $(".price").html(result.price);
            $(".package").html(result.package);
            $(".desc").html(result.desc);
            $(".link").html(result.link);
            $(".qty").html(result.qty);
            $(".type").html(result.type);
          }
          else
          {
            $("#pesan").html('<div class="alert alert-danger">'+result.msg+'</div>');
            $(".alert-danger").show();
          }
        }
      },
      error : function()
      {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
      }
    });
    /**/
  }

  function setUpgradeOption()
  {
    $("input[name='status_upgrade']").change(function(){
      var value = $(this).val();
      if(value == 2)
      {
        $(".upgrade-later").hide();
      }
      else
      {
        $(".upgrade-later").show();
      }
      // check_kupon(value);
    });
  }

  function check_kupon(status_upgrade = null){

    if(status_upgrade == null)
    {
      status_upgrade = $("input[name='status_upgrade']").val();
    }

    $.ajax({
      type: 'POST',
      url: "{{url('/check-coupon')}}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        harga : $('#price').val(),
        kupon : $('#kupon').val(),
        reseller_coupon : "",
        idpaket : $( "#select-auto-manage" ).val(),
        status_upgrade : status_upgrade,
        chat : 0,
        namapaket :  $("#select-auto-manage").find("option:selected").attr("data-paket")
      },
      dataType: 'text',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');

        var data = jQuery.parseJSON(result);

        $('#pesan').html(data.message);
        $('#pesan').show();
        if (data.message=="") {
          $('#pesan').hide();
        }
        
        if (data.status == 'success') {
          $('.total').html('IDR '+' <strike>'+formatNumber(data.price)+'</strike> '+formatNumber(data.total));
          $('#pesan').removeClass('alert-danger');
          $('#pesan').addClass('alert-success');

         if(status_upgrade == 2)
          {
            $(".upgrade").show();
            $("#package-upgrade").hide();
            $("#label-priceupgrade").hide();
            $("input[name='status_upgrade']").prop('disabled',false);
          }
          else if(data.membership == 'upgrade')
          {
            $(".upgrade").show();
            $("#package-upgrade").show();
            $("#label-priceupgrade").show();
            $("input[name='status_upgrade']").prop('disabled',false);
            $(".dayleft").html(data.dayleft);
            $("#package-upgrade").html("IDR "+formatNumber(data.upgrade_price));
            $("#label-priceupgrade").html("IDR "+formatNumber(data.packageupgrade));
          }
          else //downgrade
          {
            $("#package-upgrade").hide();
            $("#label-priceupgrade").hide();
            $("input[name='status_upgrade']").prop('disabled',true);
            $(".upgrade").hide();
          }
        } 
        /*else if (data.status == 'success-paket') {
          $('.total').html('IDR ' + formatNumber(parseInt(data.total)+parseInt(totalPriceUpgrade)));
          $('#pesan').removeClass('alert-danger');
          $('#pesan').addClass('alert-success');
          
          flagSelect = false;
          $("#select-auto-manage option").each(function() {
            console.log($(this).val());
            if ($(this).val() == data.paketid) {
              flagSelect = true;
            }
          });

          if (flagSelect == false) {
            labelPaket = data.paket;
            if (data.kodekupon=="SPECIAL12") {
              labelPaket = "Paket Special Promo 1212 - IDR 295.000";
            }
            $('#select-auto-manage').append('<option value="'+data.paketid+'" data-price="'+data.dataPrice+'" data-paket="'+data.dataPaket+'" selected="selected">'+labelPaket+'</option>');
          }
          $("#price").val(data.dataPrice);
          $("#namapaket").val(data.dataPaket);
          
          $('#select-auto-manage').val(data.paketid);
          $( "#select-auto-manage" ).change();
        }*/
        else {
          $('#pesan').removeClass('alert-success');
          $('#pesan').addClass('alert-danger');
          $('.total').html('IDR '+formatNumber(data.total));
        }
      },
      error: function(xhr,atrribute,throwable)
      {
         $('#loader').hide();
         $('.div-loading').removeClass('background-load');
         // console.log(xhr.responseText);
      }
    });
  }
  
	function formatNumber(num) {

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

  function setPricing()
  {
    var price = parseInt($("#select-auto-manage").find("option:selected").attr("data-price"));
    $('.total').html('IDR '+formatNumber(price));
  }

  function change_qty()
  {
    $("input[name='qty']").on('change keyup',function(){
      setPricing();
    });
  }

  function applyCoupon()
  {
    $(".btn-kupon").click(function(){
      var value = $("input[name='status_upgrade']:checked").val();
      check_kupon(value);

      if(value == 2)
      {
        $(".upgrade-later").hide();
      }
      else
      {
        $(".upgrade-later").show();
      }
    });
  }

  // change option when user choose package
  function manageSelectPackage()
  {
    $( "#select-auto-manage" ).change(function() {
      setPricing();
    })
  }
    
</script>
@endsection