@extends('layouts.app')

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span> Detail Pembelian </h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card-font-size list-group">
                <h5 class="alert alert-info">Anda akan membeli koin dengan detail :</h5>
                <div class="list-group-item">No Invoice : <b>{{ $row['no'] }}</b></div>
                <div class="list-group-item">Penjual : {{ $row['seller'] }}&nbsp;<span>
                @for($x=1;$x<=$row['star'];$x++)
                    <i class="fas fa-star"></i>
                @endfor
                </span></div>
                <div class="list-group-item">Metode Pembayaran : {{ $row['paymethod'] }}</div>
                <div class="list-group-item">Jumlah Coin : {{ $row['coin'] }}</div>
                <div class="list-group-item">Total : <b>{{ $row['total'] }}</b></div>

                <div class="list-group-item">
                    <a data-id="{{ $row['id'] }}" class="btn btn-gradient-info btn-sm request_buy mr-1">Request Beli</a>
                    <a href="{{ url('buy') }}" class="text-black-50">Kembali</a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="information" role="dialog">
  <div class="modal-dialog text-center">
    
    <!-- Modal content-->
    <div class="modal-content col-lg-8 col-md-9 col-sm-12 col-12 mx-auto">
      <div class="modal-header" style="display : block">
        <h5 class="mb-0">Notifikasi</h5>
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
      </div>
      <div class="modal-body">
        Maaf, jumlah transaksi anda sudah memenuhi jumlah maksimal belanja harian anda,<br/><br/>
        Jumlah transaksi <b>sukses</b> anda : <b id="total_transaction"></b><br/>
        Jumlah transaksi anda hari ini : <br/><b id="total_day"></b><br/><br/>
        Ket : <br/>
        <p>0 - 10 transaksi <b>sukses</b>, anda dapat belanja maksimal Rp 20.000/hari</p>
        <p>30 transaksi <b>sukses</b>, anda dapat belanja maksimal Rp 100.000/hari</p>
        <p>50 transaksi <b>sukses</b>, anda dapat belanja maksimal Rp 200.000/hari</p>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          {{Lang::get('custom.close')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        request_buy();
    });

    function request_buy()
    {
        $("body").on("click",".request_buy",function()
        {
            var id = $(this).attr('data-id');

            $.ajax({
                type : 'GET',
                url : "{{ url('buy-request') }}",
                dataType : 'json',
                data : {'id' : id},
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    if(result.err == 0)
                    {
                       location.href="{{ url('buy') }}";
                    }
                    else if(result.err == 1)
                    {
                        $('#loader').hide();
                        $('.div-loading').removeClass('background-load');
                        $(".err_buy").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>')
                    }
                    else
                    {
                        $('#loader').hide();
                        $('.div-loading').removeClass('background-load');
                        $("#total_day").html(result.shop_day);
                        $("#total_transaction").html(result.total);
                        $("#information").modal();
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

</script>
@endsection
