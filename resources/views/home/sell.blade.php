@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-black">{{ $lang::get('transaction.sell.title') }}</div>

                <div id="msg"><!-- message --></div>

                @if(auth()->user()->bank_name == null && auth()->user()->bank_no == null  && auth()->user()->ovo == null  && auth()->user()->gopay == null  && auth()->user()->dana == null)

                <div class="alert alert-info">Mohon isi metode pembayaran anda <a href="{{ url('account') }}">disini</a></div>

                @else
                <div class="card-body">
                    <form id="profile">

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.total.coin') }}</label>

                            <div class="col-md-6 py-2">
                                 <b id="wallet_coin">{{ $pc->pricing_format(Auth::user()->coin) }}</b>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.sell') }}</label>

                            <div class="col-md-6">
                                 <input id="amount" type="text" class="form-control" name="tr_coin" autocomplete="off" />
                                <span class="error tr_coin"><!--  --></span>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.fee') }}&nbsp;({{$fee}} %)</label>

                            <div class="col-md-6">
                                <div id="fee" class="form-control border-top-0 border-left-0 border-right-0"><!--  --></div>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.total') }}&nbsp;{{ $lang::get('custom.currency') }}</label>

                            <div class="col-md-6">
                                <div id="coin" class="form-control border-top-0 border-left-0 border-right-0 font-weight-bold"></div>
                               <span class="error wallet"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

                            <div class="col-md-6">
                                {{ $lang::get('transaction.min') }}
                            </div>
                        </div> 

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-warning">
                                    {{ $lang::get('transaction.sell.act') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            <!--  -->

             @if(auth()->user()->bank_name == null && auth()->user()->bank_no == null && auth()->user()->ovo == null && auth()->user()->gopay == null && auth()->user()->dana == null)

             @else
            <div class="card mt-4">
                <div class="card-body">
                <h4>History Penjualan</h4>
                    <div id="selling"><!--  --></div>
                </div>
            </div>
            @endif
            <!-- end col -->
        </div> 
    </div>
    <!-- end justify -->
</div>

<script type="text/javascript">
    var coin_fee = "{{ $fee }}";
    var kurs = "{{ Price::get_rate() }}";

    $(document).ready(function()
    {
        count_logic();
        sell_coin();
        display_sell();
    });

    function count_logic()
    {
        $("#amount").on("keyup",delay(function(e){
            var coin = $(this).val();
            $("#amount").val(formatNumber(coin));

            var rcoin = return_number(coin);
            fee = (rcoin * coin_fee)/100;
            $("#fee").html(formatNumber(fee));

            var total = parseFloat(kurs) * rcoin;
            total = Math.round(total);
            $("#coin").html(formatNumber(total));
            
        },100));
    }

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

    function display_sell()
    {
        $.ajax({
            type : 'GET',
            url : "{{ url('sell-list') }}",
            dataType : 'html',
            data : $(this).serialize(),
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
               $(".error").hide();
            },
            success : function(result)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".form-control").html('');
                $("#selling").html(result);
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function sell_coin()
    {
        $("#profile").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('selling') }}",
                dataType : 'json',
                data : $(this).serialize(),
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   $(".error").hide();
                },
                success : function(result)
                {
                    if(result.err == 0)
                    {
                        $("#wallet_coin").html(formatNumber(result.wallet));
                        $("input").val('');
                        display_sell();
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".tr_coin").html(result.tr_coin);
                    }
                    else if(result.err == 'trial')
                    {
                        $(".error").show();
                        $(".tr_coin").html('{!! Lang::get("custom.trial") !!} <a target="_blank" href="{{ url("account/membership") }}">{{ Lang::get("custom.here") }}</a>');
                    }
                    else
                    {
                        $(".error").show();
                        $(".tr_coin").html(result.err);
                    }
                },
                complete : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function return_number(num)
    {
       num = num.toString().replace(/\./g,'');
       num = parseInt(num);
       return num;
    }

    function formatNumber(num) 
    {
        num = num.toString().replace(/\./g,'');
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
