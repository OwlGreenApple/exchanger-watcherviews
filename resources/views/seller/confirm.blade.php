@extends('layouts.app')

@section('content')

<div class="page-header">
    <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span> Konfirmasi Penjualan </h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div id="err_message"><!--  --></div>
                <h5 class="alert alert-warning">Konfirmasi penjualan koin dengan detail :</h5>
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
                    <a type="button" class="btn bg-success text-white confirm">Konfirmasi</a>

                    @if($row['status'] == 4)
                        @if($row['seller_dispute_id'] == 0)
                            <a target="_blank" href="{{ url('seller-dispute') }}/{{ $row['id'] }}" class="text-black-50">Dispute</a>
                        @else
                            <span class="text-black-50">Tunggu Admin</span>
                        @endif
                    @endif
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
        Pastikan anda sudah menerima pembayaran sebelum klik <b>Konfirmasi</b>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn btn-success text-white" id="btn-conf" data-dismiss="modal">
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
