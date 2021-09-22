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
                <div id="buy_content"><!--  --></div>
                </div>
            </div>

        <!-- end col -->
        </div>
    </div>


<script type="text/javascript">
    $(document).ready(function()
    {
       pagination();
       history();
       search_seller();
    });

     //ajax pagination
      function pagination()
      {
          $(".page-item").removeClass('active').removeAttr('aria-current');
          var mulr = window.location.href;
          getActiveButtonByUrl(mulr)
        
          $('body').on('click', '.pagination .page-link', function (e) {
              e.preventDefault();
              var url = $(this).attr('href');
              var src = $("#search").val();
              var sort = $("select[name='sort']").val();
              var range = $("select[name='range']").val();
              // console.log(url);
              loadPagination(url,src,sort);
          });
      }

      function loadPagination(url,src,sort,range) {
          $.ajax({
            beforeSend: function()
              {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
              },
            url: url,
            data: {'src':src,'sort':sort,'range':range},
            dataType : 'html',
          }).done(function (data) {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
              getActiveButtonByUrl(url);
              $('#seller_list').html(data);
          }).fail(function (xhr,attr,throwable) {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
              $("#buy_content").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
              console.log(xhr.responseText);
          });
      }

      function getActiveButtonByUrl(url)
      {
        var page = url.split('?');
        if(page[1] !== undefined)
        {
          var pagevalue = page[1].split('=');
          $(".page-link").each(function(){
             var text = $(this).text();
             if(text == pagevalue[1])
              {
                $(this).attr('href',url);
                $(this).addClass('on');
              } else {
                $(this).removeClass('on');
              }
          });
        }
        else {
            var mod_url = url+'?page=1';
            getActiveButtonByUrl(mod_url);
        }
      }

      //end ajax pagination

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
            var sort = $("select[name='sort']").val();
            var range = $("select[name='range']").val();

            $.ajax({
                // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'GET',
                url : "{{ url('buy-list') }}",
                dataType : 'html',
                data : {'src' : src,'sort':sort,'range':range},
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
