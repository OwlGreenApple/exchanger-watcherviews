@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 style="font-size : 0.95rem" class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span>Daftar Transaksi User antar User</h3>
</div>

<div class="mb-5 main-cont">
  <div class="row card px-2 py-3">
    <div id="err"><!--  --></div>
    <div class="col-md-12 col-lg-12 table-responsive">
      <table class="table" id="order_table">
        <thead>
          <th>No</th>
          <th>Penjual</th>
          <th>Pembeli</th>
          <th>No Transaksi</th>
          <th>Total</th>
          <th>Tanggal Beli</th>
          <th>Status</th>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  <!-- order user -->
  </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="confirm_ban" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Apakah anda yakin akan me-ban user ini?
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn_confirm" data-dismiss="modal">
          Ya
        </button>
        <button class="btn" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    display_table();
    display_warning();
    ban_user();
  });

  function display_table()
  {
    $("#order_table").DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ 10, 25, 50, 75, 100, 500 ],
        "ajax": "{{ url('transaction-fetch') }}",
        "destroy": true
    });

    $('.dataTables_filter input')
     .off()
     .on('keyup keypress', delay(function() {
        $('#order_table').DataTable().search(this.value.trim(), false, false).draw();
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

  function display_warning()
  {
    $("body").on("click",".ban",function(){
      $("#confirm_ban").modal();
      $("#btn_confirm").attr('data-id',$(this).attr('id'));
    });
  }

  function ban_user(){
    $("#btn_confirm").click(function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type : 'GET',
        url : "{{ url('/user-ban') }}",
        data : {id:id},
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) 
        {
          if(result.error == 0)
          {
            $(".alert-danger").hide();
            display_table();
          }
          else
          {
            $("#err").html('<div class="alert alert-danger">'+result.msg+'</div>');
          }
        },
        complete : function()
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
      /**/
    });
  }

  /*function popup_new_window()
  {
    $( "body" ).on( "click", ".popup-newWindow", function()
    {
      event.preventDefault();
      window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
    });
  }*/
</script>
@endsection