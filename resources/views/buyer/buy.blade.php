@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">{{ Lang::get('transaction.buy.title') }}</div>

                @include('buyer.buy-form')
            </div>

            <div class="card mt-4">
                <div class="card-body">
                <h4>History Pembelian</h4>
                <div id="buy_content"><!--  --></div>
                </div>
            </div>

        <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function()
    {
       history();
       search_seller();
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
        $("#seller").DataTable();
    }

    function search_seller()
    {
        $("#btn-src").click(function(){
            var src = $("#search").val();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('buy-list') }}",
                dataType : 'html',
                data : {'data' : src},
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   $(".error").hide();
                },
                success : function(result)
                {
                    $("#seller_list").html(result);                    
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
