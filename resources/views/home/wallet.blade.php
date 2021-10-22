@extends('layouts.app')

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span> {{ Lang::get('custom.wallet') }} </h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        @if(Auth::user()->status == 3)
            <div class="card col-md-12"><div class="alert alert-danger mt-3">{{ Lang::get('auth.suspend') }}</div></div>

        @else
        <div class="card">
            <!-- <div class="card-header bg-gradient-warning"></div> -->

            <div class="col-lg-12" id="msg"><!-- message --></div>

            <div class="card-body">
                <form id="wallet_coin">
                
                    @if(auth()->user()->watcherviews_id == 0)
                      <div class="alert alert-secondary">Silahkan hubungkan akun watcherviews anda di link ini <a href="{{ url('account') }}/wallet">Connect API</a></div>
                    @else

                    <span class="error wallet"><!--  --></span>
                    <div class="form-group row">
                        <label for="name" class="col-6 col-md-4 col-form-label text-md-right">{{ Lang::get('transaction.wt') }}</label>

                        <div class="col-6 col-md-6">
                           <label for="name" class="col-form-label text-md-right"><b id="total_coin">{!! $coin !!}</b>&nbsp;coin</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-6 col-md-4 col-form-label text-md-right">{{ Lang::get('custom.coin') }}:</label>

                        <div class="col-6 col-md-6">
                           <label for="name" class="col-form-label text-md-right"><b id="coin">{{ $pc->pricing_format(Auth::user()->coin) }}</b>&nbsp;coin</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right"><!--  --></label>

                        <div class="col-md-6">
                           <div class="form-check">
                              <input class="form-check-input" type="radio" name="wallet_option" id="flexRadioDefault1" value="1" checked>
                              <label class="form-check-label" for="flexRadioDefault1">
                                {{ $lang::get('transaction.wd') }}&nbsp;(min : <b>100.000</b>)
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="wallet_option" value="2" id="flexRadioDefault2">
                              <label class="form-check-label" for="flexRadioDefault2">
                                {{ $lang::get('transaction.send') }} ke watcherviews
                              </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ Lang::get('transaction.total.coin') }}</label>

                        <div class="col-md-6">
                           <input id="amount" class="form-control" type="text" name="amount" autocomplete="off" />
                           <span class="error coin_ammount"><!--  --></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Coin Tx Fee (2.5%)</label>

                        <div class="col-md-6">
                           <div id="fee" class="form-control border-top-0 border-left-0 border-right-0"><!--  --></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Total Coin</label>

                        <div class="col-md-6">
                           <div id="total_coin_pay" class="form-control border-top-0 border-left-0 border-right-0"><!--  --></div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-gradient-info">
                                {{ Lang::get('transaction.wallet') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- endif API  -->
            @endif
        </div>
        <!-- endif AUTH -->
        @endif

        <!-- HISTORY -->
        @if(auth()->user()->watcherviews_id > 0)
        <div class="card mt-5">
            <div id="wallet_list" class="table-responsive card-body"><!--  --></div>
        </div>
        @endif
        <!--  -->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        transaction_coin();
        count_logic();
        display_wallet();
    });

    function data_table()
    {
        $("#data_transaction").DataTable({
            "ordering": false,
            "responsive": true,
        });
    }

    function display_wallet()
    {
        $.ajax({
            type : 'GET',
            url : "{{ url('wallet-list') }}",
            dataType : 'html',
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                $("#wallet_list").html(result);
            },
            complete :function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                data_table();
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function count_logic()
    {
        $("#amount").on("keyup",delay(function(e){
            var coin = $(this).val();
            $("#amount").val(formatNumber(coin));

            var rcoin = return_number(coin);
            fee = (rcoin * 25)/1000;
            $("#fee").html(formatNumber(fee));

            var total_coin = rcoin + fee;
            $("#total_coin_pay").html('<b>'+formatNumber(total_coin)+'</b>');            
        },100));
    }

    function transaction_coin()
    {
        $("#wallet_coin").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('wallet-transaction') }}",
                dataType : 'json',
                data : $(this).serialize(),
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                        $(".error").hide();
                        $(".alert-success").show();
                        cur_coin = parseInt(result.wallet_coin);
                        $("#coin").html(formatNumber(cur_coin));
                        $("#total_coin").html(formatNumber(parseInt(result.total_coin)));
                        $("#msg").html('<div class="alert alert-success text-center mt-2">{{ Lang::get("custom.success_coin") }}</div>');
                        $("input[name='amount']").val('');
                        $("#fee, #total_coin_pay").html('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}--</div>');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ $lang::get("custom.failed") }}</div>');
                    }
                    else if(result.err == 'trial')
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("custom.trial.wallet") }} <a href="{{ url("checkout") }}">{{ Lang::get("custom.here") }}</a></div>');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".coin_ammount").html(result.coin_ammount);
                    }
                    else if(result.err == 'coin')
                    {
                        $(".error").show();
                        $(".coin_ammount").html(result.coin);
                    }
                    /*else if(result.pkg !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.pkg);
                    }
                    else if(result.max !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.max);
                    }*/
                    else
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                },
                complete :function()
                {
                  $(".alert-success").delay(3000).fadeOut(2000);
                  display_wallet();
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }
</script>
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection
