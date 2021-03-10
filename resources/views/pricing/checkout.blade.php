@extends('layouts.app')
@section('content')

<div class="container" style="margin-top:50px; margin-bottom:100px">
  <div class="row justify-content-center">
    <div class="col-md-8 col-12">
      <div class="card-custom">
        <div class="card cardpad">

          <form id="proof_order">
              <h2 class="Daftar-Disini">Upgrade Membership Anda</h2>
            <!--   <div class="alert">
                <div class="bc">display bonus text here </div>
                <div class="disc">display discount text here</div>
                <h4>Fitur Drip Views <i class="ml-2 fa fa-question-circle question" aria-hidden="true"></i></h4>
              </div> -->
              <div class="form-group">
                <div class="col-12 col-md-12 mb-3">
                  <label class="text">Current Membership:</label>
                  <div class="form-control text-uppercase">@if(Auth::user()->membership == null || Auth::user()->membership == '') FREE @else {{ Auth::user()->membership }} @endif</div>
                </div>
                <div class="col-12 col-md-12">
                  <label class="text">Pilih Membership:</label>
                  <select class="form-control" name="idproof" >
                    @php $pg = 1 @endphp
                    <option @if(Request::segment(2) == 1) selected @endif data-price="{!! getPackage()[$pg]['price'] !!}" id-paket="{{ $pg }}" data-paket="{!! getPackage()[$pg]['package'] !!}" data-bc="{!! getPackage()[$pg]['bonus'] !!}" data-disc="{!! getPackage()[$pg]['disc'] !!}" selected>{!! strtoupper(getPackage()[$pg]['package']) !!} - IDR {!! str_replace(",",".",number_format(getPackage()[$pg]['price'])) !!}</option>   

                    @php $pg = 2 @endphp
                    <option @if(Request::segment(2) == 2) selected @endif data-price="{!! getPackage()[$pg]['price'] !!}" id-paket="{{ $pg }}" data-paket="{!! getPackage()[$pg]['package'] !!}" data-bc="{!! getPackage()[$pg]['bonus'] !!}" data-disc="{!! getPackage()[$pg]['disc'] !!}">{!! strtoupper(getPackage()[$pg]['package']) !!} - IDR {!! str_replace(",",".",number_format(getPackage()[$pg]['price'])) !!}</option>  

                    @php $pg = 3 @endphp
                    <option @if(Request::segment(2) == 3) selected @endif data-price="{!! getPackage()[$pg]['price'] !!}" id-paket="{{ $pg }}" data-paket="{!! getPackage()[$pg]['package'] !!}" data-bc="{!! getPackage()[$pg]['bonus'] !!}" data-disc="{!! getPackage()[$pg]['disc'] !!}">{!! strtoupper(getPackage()[$pg]['package']) !!} - IDR {!! str_replace(",",".",number_format(getPackage()[$pg]['price'])) !!}</option>  

                    <!-- <option data-price="getPackage()[$pg]['price']" data-paket="getPackage()[$pg]['package'] !!}" value=" $id "  if($id==$pg) selected endif> getActivProofPackage()[$pg]['package']  - IDR  str_replace(",",".",number_format(getActivProofPackage()[$pg]['price']))  -  str_replace(",",".",number_format(getActivProofPackage()[$pg]['credit']))  Credit</option>   -->

                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="notif"><!-- display notif --></div>
                <div class="col-md-12 col-12">
                  <label class="label-title-test">
                    Total: 
                  </label>
                  <div class="col-md-12 pl-0">
                    <span class="total" style="font-size:18px"></span>
                  </div>  
                </div>
              </div>
             
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <span class="check_mark"><!-- error --></span>
                  <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required/>
                  <label for="agree-term" class="label-agree-term text">Saya menyetujui semua pernyataan di : <a href="{{url('/helps')}}" class="term-service" target="_blank">Terms of service</a></label>
                </div>
              </div>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="submit" name="submit" id="submit" class="col-md-12 col-12 btn btn-primary bsub btn-block" value="Order Sekarang"/>
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
    change_selection();
    display_price();
    submit_order();
    tooltips();
  });

  function tooltips()
  {
    $('.question').tooltip({
      'html':true,
      'title': "Fitur Drip Views adalah Fitur untuk membagi views beberapa kali pada video yang sama dalam waktu random,sehingga penambahan views video terlihat lebih organik <br/><br/> Contoh : Video A : 200 Views untuk 11x = Total 2200 views."
    });
  }

  function change_selection()
  {
    $("select[name='idproof']").change(function(){
      display_price();
    });
  }

  function display_price()
  {
    var price = parseInt($("select[name='idproof'] option:selected").attr('data-price'));
    $(".total").html('IDR '+formatNumber(price));
    $(".bc").html('<h4>'+$("select[name='idproof'] option:selected").attr('data-bc')+'</h4>');
    $(".disc").html('<h4>'+$("select[name='idproof'] option:selected").attr('data-disc')+'</h4>');
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

  function submit_order()
  {
    $("#submit").click(function(e){
      e.preventDefault();
      var is_checked = $("#agree-term").prop('checked');
      if(is_checked == false)
      {
        $(".check_mark").html("<div class='error'>Harap centang kotak : <u>Saya menyetujui semua pernyataan</u></div>");
        return false;
      }
      else
      {
        order();
      }
     
    });
  }

  function order(){
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: "{{url('payment')}}",
      data: {
       /* price : $("select[name='idproof'] option:selected").attr('data-price'),
        package : $("select[name='idproof'] option:selected").attr('data-paket'),*/
        idpackage : $("select[name='idproof'] option:selected").attr('id-paket')
      },
      dataType: 'json',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        if(result.msg == 0)
        {
          if(result.sm == 1)
          {
            location.href="{{ url('summary') }}";
          }
          else
          {
            location.href="{{ url('thankyou') }}";
          }
        }

        if(result.msg == 1)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".notif").html('<div class="alert alert-danger">Sorry our server is too busy, please try again later.</div>');
        }

        if(result.msg == 2)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".notif").html('<div class="alert alert-danger">Invalid Package.</div>');
        }

      },
      error : function(xhr){
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        console.log(xhr.responseText);
      }
    });
  }
</script>
@endsection