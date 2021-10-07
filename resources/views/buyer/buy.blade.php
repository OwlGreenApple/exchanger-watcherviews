@extends('layouts.app')

@section('content')
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white mr-2">
          <i class="mdi mdi-cart-outline"></i>
        </span> {{ Lang::get('transaction.buy.title') }} </h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            @include('buyer.buy-form')

            <div class="card mt-4">
                <div class="card-body">
                <h4>History Pembelian</h4>
                <div class="table-responsive" id="buy_content"><!--  --></div>
                </div>
            </div>

        <!-- end col -->
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
    var url_global = "{{ url('buy-list') }}";

    $(document).ready(function()
    {
       search_seller();
       pagination();
       history();
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
                        history();
                        display_buy_list(null,null,null);
                    }
                    else if(result.err == 1)
                    {
                        $(".err_buy").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>')
                    }
                    else
                    {
                        $("#total_day").html(result.shop_day);
                        $("#total_transaction").html(result.total);
                        $("#information").modal();
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

    function history()
    {
        $.ajax({
            type : 'GET',
            url : "{{ url('buy-history') }}",
            dataType : 'html',
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
               $(".error").hide();
            },
            success : function(result)
            {
                $("#buy_content").html(result);                    
            },
            complete : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                data_table();
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function data_table()
    {
      $("#selling").DataTable({'ordering' : false});
    }

</script>
<script src="{{ asset('assets/js/buyer.js') }}" type="text/javascript"></script>
@endsection
