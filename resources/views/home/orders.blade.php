@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Orders</b></h2>  
      
      <h5>
        Show you previous history orders
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transfer-information" style="font-size: 13px; padding: 5px 8px;">
          Payment / Transfer information
        </button>        
      </h5>
      
      <hr>
    </div>

    <div class="col-md-12">
      <form class="table-responsive" id="content">
        @if(count($orders) > 0)
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
            Silhakan Transfer ke :
          </p> 
          <h2>8290-336-261</h2>
          <p class="card-text">
            BCA <b>Sugiarto Lasjim</b>
          </p>
          <p class="card-text">
            Setelah Transfer, silahkan Klik tombol <span class="badge badge-primary">confirm payment</span> di bawah ini <br> atau Email bukti Transfer anda ke <b>@php echo env('APP_EMAIL_ADMIN') @endphp</b> <br>
            Admin kami akan membantu anda max 1x24 jam
          </p>

      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Tutup
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

      <span id="error_msg"><!-- error --></span>

      <form id="formUpload">
        <div class="modal-body">
         
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
              <textarea maxlength="190" class="form-control" name="keterangan" placeholder="Max : 190 characters"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="foot">
          <input type="button" class="btn btn-primary" id="btn-confirm-ok" value="Confirm">
          <button class="btn" data-dismiss="modal">
            Cancel
          </button>
        </div>
      </form>
    </div>
      
  </div>
</div>

<!-- Modal Show Payment proof -->
<div class="modal fade" id="payment-proof" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Your payment proof
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <img class="w-100" id="pyproof" />
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    viewDetail();
    submit_payment();
    confirm_payment();
    display_payment();
  });

  function viewDetail()
  {
    $( "body" ).on( "click", ".view-details", function() {
      var id = $(this).attr('data-id');
      $('.details-'+id).toggleClass('d-none');
    });
  }
  
  function submit_payment()
  {
    $( "body" ).on( "click", "#btn-confirm-ok", function() 
    {
      var idconf = $(".btn-confirm").attr('data-id');
      var no_order = $(".btn-confirm").attr('data-no-order');
      var data = new FormData($("#formUpload")[0]);

      data.append('id_confirm',idconf);
      data.append('no_order',no_order);
     
      $.ajax({
         headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        url : "{{ url('confirm-payment') }}",
        data : data,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
        
          if(result.status == "error")
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $("#error_msg").html('<div class="alert alert-danger">'+result.message+'</div>')
          }
          else
          {
            location.href="{{ url('thank-confirm') }}";
          }
         
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    });
  }

  function display_payment()
  {
    $("body").on("click",".open_proof", function(){
      var img = $(this).attr('data-href');
      $("#pyproof").attr('src',img);
      $("#payment-proof").modal();
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

</script>
@endsection