@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-warning text-black">{{ $lang::get('transaction.sell.title') }}</div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="profile">
                    
                        <div class="float-right">{{ $lang::get('transaction.total.coin') }}&nbsp;:&nbsp;<b>{{ $pc->pricing_format(Auth::user()->coin) }}</b></div>
                        <div class="clearfix mb-2"><!--  --></div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.sell') }}</label>

                            <div class="col-md-6">
                                 <input type="number" class="form-control" min="100000" name="tr_coin" />
                                <span class="error tr_coin"><!--  --></span>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.fee') }}&nbsp;(x%)</label>

                            <div class="col-md-6">
                                <input placeholder="{{ $lang::get('transaction.placeholder') }}" type="text" class="form-control" name="tr_product" />
                                <span class="error tr_product"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.total') }}&nbsp;{{ $lang::get('custom.currency') }}</label>

                            <div class="col-md-6">
                                <div id="coin" class="form-control"></div>
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
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        withdraw_coin();
    });

    function withdraw_coin()
    {
        $("#profile").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('wallet-top-up') }}",
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
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                        var cur_coin = $("#coin").attr('data-coin');
                        cur_coin = parseInt(cur_coin);
                        cur_coin += result.coin;
                        $("#coin").html(formatNumber(cur_coin));
                        $("#msg").html('<div class="alert alert-success">{{ $lang::get("custom.success_coin") }}</div>');
                        $("input").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ $lang::get("auth.credential") }}');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ $lang::get("custom.failed") }}');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                    else if(result.pkg !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.pkg);
                    }
                    else if(result.max !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.max);
                    }
                    else
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
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
