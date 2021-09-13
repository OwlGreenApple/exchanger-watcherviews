@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-warning">
                    Konfirmasi Penjualan
                </div> 

                <div class="card-body">
                    <div id="err_message"><!--  --></div>
                    <h5>Anda akan konfirmasi penjualan koin anda dengan detail :</h5>
                    <hr/>
                    <div class="form-group">
                        <label>Nama Pembeli :</label>
                        <div class="form-control border-top-0 border-left-0 border-right-0">{{ $row['buyer_name'] }}</div>
                    </div>

                    <div class="form-group">
                        <label>No Invoice :</label>
                        <div class="form-control border-top-0 border-left-0 border-right-0">{{ $row['no'] }}</div>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Coin :</label>
                        <div class="form-control border-top-0 border-left-0 border-right-0"><b>{{ $row['coin'] }}</b></div>
                    </div>

                    <div class="form-group">
                        <label>Total :</label>
                        <div class="form-control border-top-0 border-left-0 border-right-0">{{ Lang::get('custom.currency') }}&nbsp;<b>{!! $row['total'] !!}</b></div>
                    </div>

                    <div class="form-group">
                       <label>Bukti Bayar :</label>
                       <div>{!! $row['upload'] !!}</div>
                    </div>
                    
                    <div class="form-group">
                        <a type="button" class="btn btn-warning confirm">Konfirmasi</a>
                        <a href="{{url('sell')}}" type="button" class="btn btn-warning">Kembali</a>
                        <a target="_blank" href="{{ url('seller-dispute') }}" class="btn btn-danger">Dispute</a>
                    </div>
                    
                </div>


            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="confirmation" role="dialog">
  <div class="modal-dialog text-center">
    
    <!-- Modal content-->
    <div class="modal-content col-lg-8 col-md-9 col-sm-12 col-12 mx-auto">
      <div class="modal-header" style="display : block">
        <h5 class="mb-0">Konfirmasi penjualan</h5>
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
      </div>
      <div class="modal-body">
        Apakah anda yakin akan mengkonfirmasi transaksi ini?<br/>Peringatan : pastikan bahwa pembeli <b>sudah</b> melakukan pembayaran, Karena coin <b>tidak</b> akan dikembalikan apabila anda sudah menekan tombol <b>Konfirmasi</b>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-conf" data-dismiss="modal">
          {{Lang::get('order.confirm.sell')}}
        </button>
        <button class="btn" data-dismiss="modal">
          {{Lang::get('order.cancel')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        open_popup();
        conf_act();
    });

    function open_popup()
    {
        $(".confirm").click(function(){
            $("#confirmation").modal();
        });
    }

    function conf_act()
    {
        $("#btn-conf").click(function(){
            confirm_selling();
        });
    }

    function confirm_selling()
    {
        $.ajax({
            type : 'GET',
            url : "{{ url('sell-confirmed') }}",
            dataType : 'json',
            data : {id : "{{ $row['id'] }}"},
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
                    location.href="{{ url('thank-you-sell') }}";
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#err_message").html("{{ Lang::get('custom.failed') }}");
                }
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

</script>
@endsection
