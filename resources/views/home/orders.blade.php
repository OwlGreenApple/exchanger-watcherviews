@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
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
      <form class="responsive" id="content">
        @if($orders->count() > 0)
          @include('home.order-table')
        @else
          <div class="alert bg-dashboard cardlist">
            You don't have any order yet, please make order <a href="{{ url('pricing') }}">Here</a>
          </div>
        @endif
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
            Please do Bank Transfer to :
          </p> 
          <h2>8290-336-261</h2>
          <p class="card-text">
            BCA <b>Sugiarto Lasjim</b>
          </p>
          <p class="card-text">
            After make payment, please click on confirm button <br> or transfer your payment proof to : <b>celebfans@gmail.com</b> <br>
            Our Admin will help you max 1x24 working hours.
          </p>
          <!-- <p class="card-text">
            Setelah Transfer, silahkan Klik tombol confirm payment di bawah ini <br> atau Email bukti Transfer anda ke <b>activrespon@gmail.com</b> <br>
            Admin kami akan membantu anda max 1x24 jam
          </p>
           -->

      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Ok
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
      <form id="formUpload" enctype="multipart/form-data" method="POST" action="{{ url('order-confirm-payment') }}">
        <div class="modal-body">
          @csrf
          <input type="hidden" name="id_confirm" id="id_confirm">

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>Order No</b>
            </label>

            <span class="col-md-6 col-12" id="mod-no_order">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>Package</b>
            </label>

            <span class="col-md-6 col-12" id="mod-package">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>Total</b>
            </label>

            <span class="col-md-6 col-12" id="mod-total"></span>
          </div>

          <div class="form-group" id="div-discount">
            <label class="col-md-3 col-12">
              <b>Discount</b>
            </label>

            <span class="col-md-6 col-12" id="mod-discount">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>Date</b> 
            </label>

            <span class="col-md-6 col-12" id="mod-date"></span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12 float-left">
              <b>Upload Image</b> 
            </label>

            <div class="col-md-6 col-12 float-left">
              <input type="file" name="buktibayar">
            </div>
          </div>
          <div class="clearfix mb-3"></div>
          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>Notes</b> 
            </label>
            <div class="col-md-12 col-12">
              <textarea class="form-control" name="keterangan"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="foot">
          <input type="submit" class="btn btn-primary" id="btn-confirm-ok" value="Confirm">
          <button class="btn" data-dismiss="modal">
            Cancel
          </button>
        </div>
      </form>
    </div>
      
  </div>
</div>


<script type="text/javascript">

  $(document).ready(function() {
    // refresh_page();
    viewDetail();
    searchOrder();
    confirm_payment();
  });

  /*function refresh_page(){
    $.ajax({
      type : 'GET',
      url : "<php echo url('/order/load-order') ?>",
      dataType: 'html',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        $('#content').html(result);
        // $('#pager').html(data.pager);
      },
      error : function(xhr)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        console.log(xhr.responseText);
      }
    });
  }*/

  function viewDetail()
  {
    $( "body" ).on( "click", ".view-details", function() {
      var id = $(this).attr('data-id');
      $('.details-'+id).toggleClass('d-none');
    });
  }
  
  function searchOrder()
  {
     $( "body" ).on( "click", ".btn-search", function() {
      currentPage = '';
      // refresh_page();
    });
  }
  
  function confirm_payment()
  {
    $( "body" ).on( "click", ".btn-confirm", function() {
      $('#id_confirm').val($(this).attr('data-id'));
      $('#mod-no_order').html($(this).attr('data-no-order'));
      $('#mod-package').html($(this).attr('data-package'));

      var total = parseInt($(this).attr('data-total'));
      $('#mod-total').html('Rp. ' + total.toLocaleString());
      var diskon = parseInt($(this).attr('data-discount'));
  		if (diskon == 0 ) {
  			$("#div-discount").hide();
  		}
      $('#mod-discount').html('Rp. ' + diskon.toLocaleString());
      $('#mod-date').html($(this).attr('data-date'));

      var keterangan = '-';
     // console.log($(this).attr('data-keterangan'));
      if($(this).attr('data-keterangan')!='' || $(this).attr('data-keterangan')!=null){
        keterangan = $(this).attr('data-keterangan');
      }

      $('#mod-keterangan').html(keterangan);
    });
  }

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