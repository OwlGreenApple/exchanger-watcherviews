@extends('layouts.app')

@section('content')

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white mr-2">
          <i class="mdi mdi-store-24-hour"></i>
        </span> {{ Lang::get('transaction.sell.title') }} </h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                
                <div id="msg"><!-- message --></div>

                @if(auth()->user()->bank_1 == null  && auth()->user()->bank_2 == null && auth()->user()->epayment_1 == null  && auth()->user()->epayment_2 == null  && auth()->user()->epayment_3 == null)

                <div class="alert mt-3">Mohon isi metode pembayaran anda, <b><a href="{{ url('account') }}">klik disini</a></b></div>

                @elseif(Auth::user()->status == 3)

                <div class="col-md-12"><div class="alert alert-danger mt-3">{{ Lang::get('auth.suspend') }}</div></div>

                @else
                <div class="card-body">
                    <form id="profile">

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Balance Coin</label>

                            <div class="col-md-6 py-2">
                                 <span id="wallet_coin">{{ $pc->pricing_format(Auth::user()->coin) }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Jual coin</label>

                            <div class="col-md-6">
                                 <input id="amount" type="text" class="form-control" name="tr_coin" autocomplete="off" placeholder="{{ Lang::get('transaction.min') }}" />
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
                            <label class="col-md-4 col-form-label text-md-right">Total Coin</label>

                            <div class="col-md-6">
                                <div id="total_coin_pay" class="form-control border-top-0 border-left-0 border-right-0"><!--  --></div>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Total&nbsp;{{ $lang::get('custom.currency') }}</label>

                            <div class="col-md-6">
                                <div id="coin" class="form-control border-top-0 border-left-0 border-right-0 font-weight-bold"></div>
                               <span class="error wallet"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn bg-gradient-warning">
                                    {{ $lang::get('transaction.sell.act') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            <!--  -->

             @if(auth()->user()->bank_1 == null && auth()->user()->bank_2 == null && auth()->user()->epayment_1 == null && auth()->user()->epayment_2 == null && auth()->user()->epayment_3 == null)

             @else
            <div class="card mt-4">
                <div class="card-body">
                <h4>History Penjualan</h4>
                    <div id="err_msg"><!-- error --></div>
                    <div id="selling" class="table-responsive"><!--  --></div>
                </div>
            </div>
            @endif
            <!-- end col -->
        </div> 
    </div>
    <!-- end justify -->


<!-- Modal Delete -->
<div class="modal fade" id="del_sell" role="dialog">
  <div class="modal-dialog text-center">
    
    <!-- Modal content-->
    <div class="modal-content col-lg-8 col-md-9 col-sm-12 col-12 mx-auto">
      <div class="modal-header" style="display : block">
        <h5 class="mb-0">Hapus penjualan</h5>
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
      </div>
      <div class="modal-body">
        Apakah anda yakin akan menghapus transaksi ini?<br/><b>Peringatan :</b> Jumlah coin yang kembali ke <b>wallet</b> hanya jumlah coin yang dijual <b>(total coin)</b>, <b>tidak</b> termasuk biaya coin <b>(coin fee).</b>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-del" data-dismiss="modal">
          {{$lang::get('order.yes')}}
        </button>
        <button class="btn" data-dismiss="modal">
          {{$lang::get('order.cancel')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">
    var coin_fee = "{{ $fee }}";
    var kurs = "{{ Price::get_rate() }}";

    $(document).ready(function()
    {
        count_logic();
        sell_coin();
        display_sell();
        del_popup();
        del_act();
    });

    function data_table()
    {
        $("#sell_list").DataTable({
            "ordering": false,
            "responsive": true,
        });
    }

    function count_logic()
    {
        $("#amount").on("keyup",delay(function(e){
            var coin = $(this).val();
            $("#amount").val(formatNumber(coin));

            var rcoin = return_number(coin);
            fee = (rcoin * coin_fee)/100;
            $("#fee").html(formatNumber(fee));

            var total_coin = rcoin + fee;
            $("#total_coin_pay").html(formatNumber(total_coin));

            var total = parseFloat(kurs) * rcoin;
            total = Math.round(total);
            $("#coin").html(formatNumber(total));
            
        },100));
    }

    function del_popup()
    {
        $("body").on("click",".del_sell",function(){
            var id = $(this).attr('data-id');
            $("#btn-del").attr('data-id',id);
            $("#del_sell").modal();
        });
    }

    function del_act()
    {
        $("#btn-del").click(function(){ 
            var id = $(this).attr('data-id');
            delete_sell(id) 
        });
    }

    function delete_sell(id)
    {
        $.ajax({
            type : 'GET',
            url : "{{ url('sell-del') }}",
            dataType : 'json',
            data : {id : id},
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
                    $(".alert").hide();
                    $("#wallet_coin").html(formatNumber(result.wallet));
                    display_sell();
                }
                else if(result.err == 'trial')
                {
                    $(".alert").show();
                    $("#err_msg").html('<div class="alert alert-warning">{{ Lang::get("custom.trial.wallet") }} <a href="{{ url("account") }}/membership">{{ Lang::get("custom.here") }}</a></div>');
                }
                else
                {
                    $(".alert").show();
                    $("#err_msg").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>')
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
            },
            success : function(result)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".form-control").html('');
                $("#selling").html(result);
            },
            complete : function()
            {
                data_table();
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
</script>
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection