@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-black">{{ $lang::get('transaction.buy.title') }}</div>

                <div class="mt-3 col-md-12 mb-3">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card text-white bg-primary" style="max-width: 18rem;">
                              <div class="card-header">Total Penjualan</div>
                              <div class="card-body">
                                <h2 class="card-title">Rp100.000</h2>
                              </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card text-white bg-primary col-lg-6" style="max-width: 18rem;">
                              <div class="card-header">Total Coins</div>
                              <div class="card-body">
                                <h2 class="card-title">100.000</h2>
                              </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="card-body table-responsive">
                    <div>Transfer Uang <span class="badge badge-primary">0</span></div>     
                    <div>Pembayaran <span class="badge badge-success">0</span></div>     
                    <div>Konfirmasi uang diterima <span class="badge badge-warning">0</span></div>     
                    <div>Transfer Coin <span class="badge badge-info">0</span></div>     
                </div>

                <div class="card-body table-responsive">
                    <input type="text" class="form-control" />
                    <button type="button" class="btn-primary">Cari Penjual</button>
                </div>

                <div class="card-body table-responsive">
                    <table id="seller" class="table table-hover shopping-cart-wrap">
                        <thead class="text-muted">
                        <tr>
                          <th scope="col" width="240">{{ $lang::get('transaction.buy.seller') }}</th>
                          <th scope="col">{{ $lang::get('transaction.qty') }}</th>
                          <th scope="col">{{ $lang::get('transaction.price') }}</th>
                          <th scope="col">{{ $lang::get('transaction.rate') }}</th>
                          <th scope="col">{{ $lang::get('transaction.comments') }}</th>
                          <th scope="col" width="120" class="text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><h6 class="title text-truncate">Penjual A</h6></td>
                            <td> 100.000</td>
                            <td> 
                                <div class="price-wrap"> 
                                    <var class="price">IDR 10.000</var> 
                                   <!--  <small class="text-muted">(USD5 each)</small>  -->
                                </div> <!-- price-wrap .// -->
                            </td>
                            <td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"><i class="fas fa-star"></td>
                            <td><i class="far fa-envelope"></i></td>
                            <td class="text-right"> 
                            <a class="btn btn-outline-success btn-sm conf" data-toggle="tooltip" data-original-title="Save to Wishlist">Beli</a> 
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--  -->
                </div>
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
        open_popup();
        withdraw_coin();
    });

    function data_table()
    {
        $("#seller").DataTable();
    }

    function open_popup()
    {
        $("body").on('click','.conf',function(){
            $("#confirm_buy").modal();
        });
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
