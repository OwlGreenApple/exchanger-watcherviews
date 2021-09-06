@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">{{ $lang::get('transaction.buy.title') }}</div>

                 <!-- search -->
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-lg-8">
                            <form class="card card-sm">
                                <div class="card-body row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <i class="fas fa-search h4 text-body"></i>
                                    </div>
                                    <!--end of col-->
                                    <div class="col">
                                        <input class="form-control form-control form-control-borderless" type="search" placeholder="Cari Coin">
                                    </div>
                                    <!--end of col-->
                                    <div class="col-auto">
                                        <button class="btn btn-success" type="submit">Cari</button>
                                    </div>
                                    <!--end of col-->
                                </div>
                            </form>
                        </div>
                        <!--end of col-->
                    </div>
                </div>

                <div class="card-body table-responsive">
                    <table id="seller" class="table table-hover shopping-cart-wrap">
                        <thead class="text-muted">
                        <tr>
                          <th scope="col" width="240">{{ $lang::get('transaction.buy.no') }}</th>
                          <th scope="col">{{ $lang::get('transaction.qty') }}</th>
                          <th scope="col">{{ $lang::get('transaction.price') }}</th>
                          <th scope="col">{{ $lang::get('transaction.rate') }}</th>
                          <th scope="col">{{ $lang::get('transaction.comments') }}</th>
                          <th scope="col" width="120" class="text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><h6 class="title text-truncate">B-210903-002</h6></td>
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
                            <a href="{{ url('buy-detail') }}" class="btn btn-outline-success btn-sm conf" data-toggle="tooltip" data-original-title="Save to Wishlist">Beli</a> 
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--  -->
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                <h4>History Pembelian</h4>
                <table class="table" id="selling">
                    <thead>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Total Coin</th>
                        <th>Kurs</th>
                        <th>Harga</th>
                        <th>Komentar</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2021-09-02</td>
                            <td>B-210902-001</td>
                            <td>500.000</td>
                            <td>0.1</td>
                            <td>Rp 50.000</td>
                            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
                            <td>
                                <a target="_blank" href="{{ url('buyer-confirm') }}" class="btn btn-primary btn-sm">Konfirmasi</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2021-09-02</td>
                            <td>B-210903-001</td>
                            <td>100.000</td>
                            <td>0.1</td>
                            <td>Rp 10.000</td>
                            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
                            <td>
                                <a target="_blank" href="{{ url('buyer-dispute') }}" class="btn btn-warning btn-sm">Dispute</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>

        <!-- end col -->
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
