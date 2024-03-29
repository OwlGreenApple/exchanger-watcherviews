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
                <h5 class="alert alert-warning">Order Pembeli :</h5>
                <div class="list-group-item">No : <b>{{ $row['no'] }}</b></div>
                <div class="list-group-item">Pembeli : {{ $row['buyer'] }}</div>

                <div data-toggle="collapse" data-target="#collapseReputation" aria-expanded="false" aria-controls="collapseReputation" class="list-group-item">Reputasi : <i class="fas fa-question-circle reputation"></i>&nbsp; 
                    <i style="font-size : 20px" class="fas fa-caret-down float-right"></i>
                    <div class="clearfix"><!--  --></div>
                </div>
                <span id="collapseReputation" class="collapse list-group-item">
                    <!-- collapse -->
                    <div class="list-group-item">Rating : <span>
                    @if($row['rate'] > 0)
                        @for($x=1;$x<=$row['rate'];$x++)
                          <i class="fas fa-star"></i>
                          @if($x == $row['rate'] && $row['rate'] < 5)
                            @if($row['star_float'] == 0)
                              <i class="fas fa-star"></i>
                            @else
                              <i class="fas fa-star-half"></i>
                            @endif
                          @endif
                        @endfor<span>|</span>
                    @endif
                    </span><span><a href="{!! $row['link_comment'] !!}" class="mt-2"><i class="far fa-envelope"></i></a></span></div>
                    <div class="list-group-item">Warning <i class="fas fa-question-circle w-tool"></i>&nbsp;: {!! $row['warning'] !!}</div>
                    <div class="list-group-item">Suspend <i class="fas fa-question-circle s-tool"></i>&nbsp;: {!! $row['suspend'] !!}</div>
                    <!-- end collapse -->
                </span>
                <div class="list-group-item">Jumlah Coin : {{ $row['coin'] }}</div>
                <div class="list-group-item">Total : <b>{{ $row['total'] }}</b></div>

                <div class="list-group-item">
                    <a data-id="{{ $row['id'] }}" data-act="1" class="btn btn-warning mr-3 sell text-dark">Terima</a>
                    <a data-id="{{ $row['id'] }}" data-act="2" type="button" class=" text-50-black mr-3 sell"><small>Tolak</small></a>
                    <a data-id="{{ $row['id'] }}" data-act="3" type="button" class="text-50-black sell"><small>Block</small></a>
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
        <button id="btn_accept" class="btn" data-dismiss="modal">
          <!-- text -->
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
        display_tooltip();
    });

    function display_tooltip()
    {
        $(".w-tool").tooltip({
            'title' : '{!! Lang::get("auth.warning") !!}'
        });

        $(".s-tool").tooltip({
            'title' : '{!! Lang::get("auth.suspend-tooltip") !!}'
        });

        $(".reputation").tooltip({
            'title' : '{!! Lang::get("transaction.reputation") !!}'
        });
    }

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
        $("#btn_accept").html('Terima');
        $("#btn_accept").removeClass('btn-danger text-white');
        $("#btn_accept").addClass('btn-warning text-dark');
    }

    function message_refuse()
    {
        var msg = '';
        msg += 'Apakah anda yakin untuk <b>menolak</b> request order ini?';
        $("#message").html(msg);
        $("#btn_accept").html('Tolak');
        $("#btn_accept").removeClass('btn-warning text-dark');
        $("#btn_accept").addClass('btn-danger text-white');
    }

    function message_block()
    {
        var msg = '';
        msg += '<b>Peringatan : </b>Jika anda memblock pembeli ini, maka semua transaksi yang berhubungan dengan pembeli ini akan di-batalkan.<br>';
        $("#message").html(msg);
        $("#btn_accept").html('Block');
        $("#btn_accept").removeClass('btn-warning text-dark');
        $("#btn_accept").addClass('btn-danger text-white');
    }

</script>
@endsection
