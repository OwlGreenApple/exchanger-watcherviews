@extends('layouts.app')

@section('content')
<link href="{{ asset('/public/assets/css/order.css') }}" rel="stylesheet" />

<div class="container mb-5 main-cont" style="">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Orders</b></h2>  
      
      <h5>
        Show you previous history orders
        <button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#transfer-information" style="font-size: 13px; padding: 5px 8px;">
          Payment / Transfer information
        </button>        
      </h5>
      
      <hr>
    </div>
      @if (session('error') )
    <div class="col-md-12 ">
      <div id="pesan" class="alert alert-danger">
          {{session('error')}}
        </div>
    </div>
      @endif

    <div class="col-md-12">
      <form class="table-responsive">
        <table class="table table-bordered w-100" style="font-size : 0.65rem" id="data_order">
          <thead>
            <th class="menu-nomobile">
             {{$lang::get('order.no')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.package')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.yt_thumb')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.url')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.price')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.total')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.purchased_view')}}
            </th> 
            <th class="menu-nomobile">
              {{$lang::get('order.start')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.views')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.date')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.date_complete')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('order.proof')}}
            </th>
            <th class="header" action="status">
              {{$lang::get('order.status')}}
            </th>
          </thead>
          <tbody></tbody>
        </table>
       <!--  -->
      </form>
    </div>

  </div>
</div>

<!-- Modal Transfer Information -->
<div class="modal fade" id="transfer-information" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Transfer Information
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

          <p class="card-text">
            Silahkan melakukan Transfer Bank ke
          </p> 
          <h2>8290-336-261</h2>
          <p class="card-text">
            BCA <b>Sugiarto Lasjim</b>
          </p>
          <p class="card-text">
            Setelah Transfer, silahkan Klik tombol confirm payment di bawah ini <br> atau Email bukti Transfer anda ke <b>{{ env('EMAIL_ADMIN') }}</b> <br>
            Admin kami akan membantu anda max 1x24 jam
          </p>

      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Ok
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="confirm-repromote" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <div id="pesan"><!--  --></div>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="re-title"><!--  --></div>
        <div>{{$lang::get('order.package')}} : <span id="order-package"></span></div>
        <div>{{$lang::get('order.price')}} : <span id="order-price"></span></div>
        <div>{{$lang::get('order.total_views')}} : <span id="order-total_views"></span></div>
        <div>{{$lang::get('order.total')}} : <span id="order-total"></span></div>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-promote" data-dismiss="modal">
          {{$lang::get('order.yes')}}
        </button>
        <button class="btn" data-dismiss="modal">
          {{$lang::get('order.cancel')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- Modal Confirm payment -->
<div class="modal fade" id="confirm-payment" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Confirm payment
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form enctype="multipart/form-data" method="POST" action="{{ url('order-confirm-payment') }}">
        <div class="modal-body">
          @csrf
          <input type="hidden" name="id_confirm" id="id_confirm">

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.no')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-no_order">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.package')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-package">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.total')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-total"></span>
          </div>

          <div class="form-group" id="div-purchased_view">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.purchased_view')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-purchased_view">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.date')}}</b> 
            </label>

            <span class="col-md-6 col-12" id="mod-date"></span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12 float-left">
              <b>{{$lang::get('order.upload')}}</b> 
            </label>

            <div class="col-md-6 col-12 float-left">
              <input type="file" name="buktibayar">
            </div>
          </div>
          <div class="clearfix mb-3"></div>
          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.notes')}}</b> 
            </label>
            <div class="col-md-12 col-12">
              <textarea class="form-control" name="keterangan"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="foot">
          <input type="submit" class="btn btn-primary" id="btn-confirm-ok" value="{{$lang::get('order.confirm')}}">
          <button type="submit" class="btn" data-dismiss="modal">
            {{$lang::get('order.cancel')}}
          </button>
        </div>
      </form>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    load_page();
    open_repromote();
    repromote();
    refill();
  });

  function load_page()
  {
    $("#data_order").DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ 10, 25, 50, 75, 100, 500 ],
        "ajax": "{{ url('order-list') }}",
        "destroy": true
    });

    $('.dataTables_filter input')
     .off()
     .on('keyup', delay(function() {
        $('#data_order').DataTable().search(this.value.trim(), false, false).draw();
     },1000));    
  }

  function delay(callback, ms) {
      var timer = 0;
      return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
  }

  // RE-PROMOTE-
  function open_repromote()
  {
    $("body").on("click",".repromote",function()
    {
      var id = $(this).attr('id'); 
      var txt = $(this).attr('data-text');
      var package = $(this).attr('data-package'); 
      var price = $(this).attr('data-price');
      var total_views = $(this).attr('data-purchased-view');
      var total = $(this).attr('data-total');

      $("#btn-promote").attr('data-id',id);
      $("#re-title").html("<h6>"+txt+"</h6>");
      $("#order-package").html("<b>"+package+"</b>");
      $("#order-price").html("<b>Rp."+formatNumber(price)+"</b>");
      $("#order-total_views").html("<b>"+formatNumber(total_views)+"</b>");
      $("#order-total").html("<b>Rp."+formatNumber(total)+"</b>");
      $("#confirm-repromote").modal();
    });
  }

  function formatNumber(num) {

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

  // RE-PROMOTE-ACT
  function repromote()
  {
    $("body").on("click","#btn-promote",function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type: 'GET',
        url: "{{url('order-repromote')}}",
        data: { "id" : id },
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(data) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(data.result == 1)
          {
            $("#confirm-repromote").modal('hide');
            load_page();
          }
          else
          {
            $("#pesan").html('<div class="alert alert-danger">{{ $lang::get("custom.inv_id") }}</div>')
          }
        },
        error : function()
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        }
      });
      /**/
    });
  }

  //REFILL 
  function refill()
  {
    $("body").on("click",".refill",function(){
      var id = $(this).attr('id');

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: "{{url('order-refill')}}",
        data: {"id" : id},
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(data) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(data.result == 1)
          {
            load_page();
          }
          else
          {
            $("#pesan").html('<div class="alert alert-danger">{{ $lang::get("custom.inv_id") }}</div>')
          }
        },
        error : function()
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        }
      });
      /**/
    });
  }

  $( "body" ).on( "click", ".view-details", function() {
    var id = $(this).attr('data-id');

    $('.details-'+id).toggleClass('d-none');
  });
  
  $( "body" ).on( "click", ".btn-search", function() {
    currentPage = '';
    refresh_page();
  });

  $( "body" ).on( "click", ".btn-confirm", function() {
    $('#id_confirm').val($(this).attr('data-id'));
    $('#mod-no_order').html($(this).attr('data-no-order'));
    $('#mod-package').html($(this).attr('data-package'));

    var total = parseInt($(this).attr('data-total'));
    $('#mod-total').html('Rp. ' + total.toLocaleString());
    $('#mod-purchased_view').html(parseInt($(this).attr('data-purchased-view')).toLocaleString()); 
    /*var diskon = parseInt($(this).attr('data-discount'));
		if (diskon == 0 ) {
			$("#div-discount").hide();
		}
    $('#mod-discount').html('Rp. ' + diskon.toLocaleString());*/
    $('#mod-date').html($(this).attr('data-date'));

    var keterangan = '-';
   // console.log($(this).attr('data-keterangan'));
    if($(this).attr('data-keterangan')!='' || $(this).attr('data-keterangan')!=null){
      keterangan = $(this).attr('data-keterangan');
    }

    $('#mod-keterangan').html(keterangan);
  });

  // $( "body" ).on( "click", "#btn-confirm-ok", function() 
  // {
    // confirm_payment();
  // });

  $( "body" ).on( "click", ".popup-newWindow", function()
  {
    event.preventDefault();
    window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
  });

  $( "body" ).on( "click", ".btn-delete", function() {
    $('#id_delete').val($(this).attr('data-id'));
  });

  $( "body" ).on( "click", "#btn-delete-ok", function() {
    delete_order();
  });

  $(document).on('click', '.checkAll', function (e) {
    $('input:checkbox').not(this).prop('checked', this.checked);
  });

</script>
@endsection