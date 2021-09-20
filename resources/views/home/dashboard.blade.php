@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card px-3 py-3">
                <div class="col-lg-12 text-right">
                     <div><a class="text-danger" href="{{ url('account') }}/wallet"><i class="fas fa-plug"></i>&nbsp;Harap Connect API</a></div>
                    <div class="text-primary"><i class="fas fa-plug"></i>&nbsp;API Connected</div>
                </div>
                <div class="mt-3 col-md-12 mb-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card text-white text-center bg-primary ml-auto" style="max-width: 18rem;">
                              <div class="card-header"><a class="text-white" href="{{ url('buy') }}">Total Penjualan</a></div>
                              <div class="card-body">
                                <h2 class="card-title">Rp100.000</h2>
                              </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card text-white text-center bg-success ml-auto" style="max-width: 18rem;">
                              <div class="card-header"><a class="text-white" href="{{ url('buy') }}">Total Pembelian</a></div>
                              <div class="card-body">
                                <h2 class="card-title">Rp100.000</h2>
                              </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card text-dark text-center bg-warning" style="max-width: 18rem;">
                              <div class="card-header"><a class="text-dark" href="{{ url('wallet') }}">My Coins</a></div>
                              <div class="card-body">
                                <h2 class="card-title">100.000</h2>
                              </div>
                            </div>
                        </div>

                    </div>
                    
                </div>

                <div class="card-body">
                    <h5 class="alert text-center">Notifikasi</h5>
                    <div class="row">
                        <div class="col-lg-3 text-center"><a href="{{ url('buy') }}" class="btn btn-outline-secondary d-block p-2">Transfer Uang <span class="badge badge-primary">0</span></a></div>  
                        <div class="col-lg-3 text-center"><a href="{{ url('buy') }}" class="btn btn-outline-secondary d-block p-2">Pembayaran <span class="badge badge-success">0</span></a></div>  
                        <div class="col-lg-3 text-center"><a href="{{ url('sell') }}" class="btn btn-outline-secondary d-block p-2">Konfirmasi uang diterima <span class="badge badge-warning">0</span></a></div>  
                        <div class="col-lg-3 text-center"><a href="{{ url('sell') }}" class="btn btn-outline-secondary d-block p-2">Transfer Coin <span class="badge badge-danger">0</span></a></div>   
                    </div>   
                </div>

                <!-- buy page -->
                @include('buyer.buy-form')
                <!--  -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation -->
<div class="modal fade" id="confirm_buy" role="dialog">
  <div class="modal-dialog text-center">
    
    <!-- Modal content-->
    <div class="modal-content col-lg-8 col-md-9 col-sm-12 col-12 mx-auto">
      <div class="modal-header" style="display : block">
        <h5 class="mb-0">{{$lang::get('transaction.conf')}}</h5>
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
      </div>
      <div class="modal-body">
        <div>Penjual : Test</div>
        <div><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <div>Jumlah Coin : 100.000</div>
        <div>Total : <b>Rp 10.000</b></div>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-promote" data-dismiss="modal">
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
    $(document).ready(function()
    {
        // data_table();
        withdraw_coin();
    });

    function data_table()
    {
        $("#seller").DataTable();
    }

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
