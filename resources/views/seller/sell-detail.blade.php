@extends('layouts.app')

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span> Detail Pembeli </h3>
</div>

<div class="row justify-content-center">

    <div class="col-md-12">
        <div class="card">
            <div class="card-body card-font-size list-group">
                <h5 class="alert alert-warning">Koin anda di order oleh pembeli dengan detail :</h5>
                <div class="list-group-item">No Invoice : <b>{{ $row['no'] }}</b></div>
                <div class="list-group-item">Pembeli : {{ $row['buyer'] }}</div>
                <div class="list-group-item">Rating : <span>
                @if($row['star'] > 0)
                    @for($x=1;$x<=$row['star'];$x++)
                        <i class="fas fa-star"></i>
                    @endfor <span>|</span>
                @endif
                </span><span><a class="mt-2" href=""><i class="far fa-envelope"></i></a></span></div>
                <div class="list-group-item">Warning : {!! $row['warning'] !!}</div>
                <div class="list-group-item">Suspend : {!! $row['suspend'] !!}</div>
                <div class="list-group-item">Jumlah Coin : {{ $row['coin'] }}</div>
                <div class="list-group-item">Total : <b>{{ $row['total'] }}</b></div>

                <div class="list-group-item">
                    <a data-id="{{ $row['id'] }}" data-act="1" class="btn btn-warning sell text-dark">Terima Request</a>
                    <a data-id="{{ $row['id'] }}" data-act="2" type="button" class="btn btn-warning text-dark sell">Tolak Request</a>
                    <a data-id="{{ $row['id'] }}" data-act="3" type="button" class="btn btn-danger sell">Block Pembeli</a>
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
      <div id="message" class="modal-body"><!--  --></div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button id="btn_accept" class="btn btn-info" data-dismiss="modal">
          {{Lang::get('order.yes')}}
        </button>
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
        $("body").on("click",".sell",function()
        {
            var id = $(this).attr('data-id');
            var act = $(this).attr('data-act');

            if(act == 1)
            {
                message_accept();
            }
            else if(act == 2)
            {
                message_refuse();
            }
            else
            {
                message_block();
            }

            $("#btn_accept").attr({'data-id':id,'data-act':act});
            $("#information").modal();
        });

        $("body").on("click","#btn_accept",function()
        {
            var id = $(this).attr('data-id');
            var act = $(this).attr('data-act');

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('seller-decision') }}",
                dataType : 'json',
                data : {'id' : id,"act" : act},
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    if(result.err == 0)
                    {
                       location.href="{{ url('sell') }}";
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

    function message_accept()
    {
        var msg = '';
        msg += 'Apakah anda yakin untuk <b>menerima</b> request order ini?';
        $("#message").html(msg);
    }

    function message_refuse()
    {
        var msg = '';
        msg += 'Apakah anda yakin untuk <b>menolak</b> request order ini?';
        $("#message").html(msg);
    }

    function message_block()
    {
        var msg = '';
        msg += 'Anda akan memblock pembeli ini,<br>';
        msg += '<b>Peringatan : </b>Jika anda memblock pembeli ini, maka semua transaksi dengan status <b>Lihat request / belum di setujui</b> oleh anda juga akan <b>di-batalkan</b>.<br>';
        msg += 'Apakah anda sudah yakin untuk <b>memblock</b> pembeli ini?';
        $("#message").html(msg);
    }

</script>
@endsection
