@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>User Orders</b></h2>  
    
      <hr>
    </div>

    <div class="col-md-12">
      <form class="responsive">
          <table class="table" id="order-table">
              <thead align="center">
                <th>No Order</th>
                <th>Package</th>
                <th>Purchased Coins</th>
                <th>Price</th>
                <th>Total</th>
                <th>Date</th>
                <th>Uploaded Image</th>
                <th>Notes</th>
                <th style="width:145px">Status</th>
              </thead>

              <tbody id="content">@include('admin.table')</tbody>
          </table>
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
          User payment proof
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

<!-- Modal Confirm Order -->
<div class="modal fade" id="confirm-order" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Confirm Order
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Confirm order no : <span class="order_number"><!-- no order --></span>? 
        <input type="hidden" name="id_confirm" id="id_confirm">
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-confirm-ok" data-dismiss="modal">
          Confirm
        </button>
        <button class="btn" data-dismiss="modal">
          Cancel
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    tables();
    confirm_payment();
    display_payment();
    confirm_order();
  });

  function tables()
  {
    $("#order-table").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting": [] //prevent datatable from automatic sorting
    });
  }

  function confirm_payment()
  {
    $( "body" ).on( "click", ".btn-confirm", function() 
    {
      var no_order = $(this).attr('data-no-order');
      $('#id_confirm').val($(this).attr('data-id'));
      $(".order_number").html('<b>'+no_order+'</b>');
      $("#confirm-order").modal();
    });
  }
  
  function confirm_order()
  {
    $( "body" ).on( "click", "#btn-confirm-ok", function() 
    {
      $.ajax({
         headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        url : "{{ url('confirm-order') }}",
        data : { 'id_confirm' : $("#id_confirm").val() },
        dataType: 'html',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
        
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#content").html(result);
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

</script>
@endsection