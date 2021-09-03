@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont" style="">
  <div class="row">

    <div class="col-md-12">
      <h2><b>Users</b></h2>  
      <h5>Daftar User</h5>
      <hr>
      <div id="err"><!--  --></div>
    </div>

    <div class="col-4 col-md-12 col-lg-12 table-responsive">
      <table class="table" id="order_table">
        <thead>
          <th>No</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Hp</th>
          <th>Membership</th>
          <th>Trial</th>
          <th>Masa berlaku membership</th>
          <th>Tanggal Gabung</th>
          <th>Action</th>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  <!-- order user -->
  </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="confirm_popup" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Konfirmasi Pembayaran
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

<!-- Modal Cancel -->
<div class="modal fade" id="cancel_popup" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Batal Order?
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="btn_cancel" data-dismiss="modal">
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
    /*
    enable_id_confirm();
    confirm_order();
    cancel_order();
    popup_new_window();
    display_no_order();
    confirm_tiktok();*/
  });

  function display_table()
  {
    $("#order_table").DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ 10, 25, 50, 75, 100, 500 ],
        "ajax": "{{ url('user-fetch') }}",
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

  function enable_id_confirm()
  {
    $("body").on("click",".confirm",function(){
      var id = $(this).attr('data-id');
      $("#btn_confirm").attr('data-id',id);
    });

    $("body").on("click",".cancel",function(){
      var id = $(this).attr('data-id');
      $("#btn_cancel").attr('data-id',id);
    });
  }

  function confirm_order(){
    $("#btn_confirm").click(function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type : 'GET',
        url : "{{ url('/order-confirm') }}",
        data : {id:id},
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.success == 1)
          {
            $(".alert-danger").hide();
            display_table();
          }
          else
          {
            $("#err").html('<div class="alert alert-danger">'+result.msg+'</div>');
          }
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

  function display_no_order()
  {
    $("body").on('click','.confirm_tiktok',function()
    {
      var no_order = $(this).attr('data-no');
      var id = $(this).attr('data-id');
      $("#btn_confirm_tiktok").attr('data-id',id);
      $("#no_order").html(no_order);
    });
  }

  function confirm_tiktok(){
    $("#btn_confirm_tiktok").click(function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type : 'GET',
        url : "{{ url('/confirm-tiktok') }}",
        data : {id:id},
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.success == 1)
          {
            $(".alert-danger").hide();
            display_table();
          }
          else
          {
            $("#err").html('<div class="alert alert-danger">'+result.msg+'</div>');
          }
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

  function cancel_order()
  {
    $("#btn_cancel").click(function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type : 'GET',
        url : "{{ url('/order-cancel') }}",
        data : {id:id},
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.success == 1)
          {
            $(".alert-danger").hide();
            display_table();
          }
          else
          {
            $("#err").html('<div class="alert alert-danger">'+result.msg+'</div>');
          }
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

  function popup_new_window()
  {
    $( "body" ).on( "click", ".popup-newWindow", function()
    {
      event.preventDefault();
      window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
    });
  }
</script>
@endsection