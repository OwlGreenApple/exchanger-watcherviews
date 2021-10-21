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

<script type="text/javascript">
    var url_global = "{{ url('buy-list') }}";

    $(document).ready(function()
    {
       display_seller();
       search_seller();
       pagination();
       history();
    });

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
