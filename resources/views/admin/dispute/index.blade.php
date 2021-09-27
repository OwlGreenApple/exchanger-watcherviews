@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">

    <div class="col-md-12">
      <h2><b>Dispute</b></h2>  
      <h5>Daftar dispute</h5>
      <hr>
      <div id="err"><!--  --></div>
    </div>

    <div id="dispute_table" class="col-4 col-md-12 col-lg-12 table-responsive px-4 py-4 bg-white">
      <!-- display dispute -->
    </div>
  <!--  -->
  </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detail_user" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Detail <span id="role_popup"><!-- display role --></span>
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div>Nama : <span id="name_popup"><!-- display user name --></span></div>
        <div>Tanggal Dispute : <span id="date_dispute_popup"><!-- display date dispute --></span></div>
        <div><a id="identity_popup" class="popup-newWindow">Lihat Bukti Identitas</a></div>
        <div><a id="proof_popup" class="popup-newWindow">Lihat Bukti Transfer</a></div>
        <div><a id="mutation_popup" class="popup-newWindow">Lihat Bukti Mutasi</a></div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="confirm_ban" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Apakah anda yakin akan menerima dispute dari user ini?
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

  $(document).ready(function() 
  {
    detail_user();
    dispute_notify();
    dispute_form();
    dispute_user();
    display_table()
    popup_new_window();
    notify_user();
  });

  function dispute_notify()
  {
    $("body").on("click",".bell",function(){
      var buyer_id = $(this).attr('data-buyer');
      var seller_id = $(this).attr('data-seller');
      var trans_id = $(this).attr('data-tr-id');
      var data = {'data_buyer' : buyer_id, 'data_seller' : seller_id,'data_trid' : trans_id}
      
      $.ajax({
        type : 'GET',
        url : "{{ url('dispute-notify-users') }}",
        data : data,
        dataType: 'json',
        beforeSend: function() 
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) 
        {
          if(result.err == 0)
          {
            $("#err").html('<div class="alert alert-success">Dispute notifikasi sudah terkirim</div>');
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
          $(".alert").hide();
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

  function dispute_form()
  {
    $("body").on("click",".blame",function()
    {
      var buyer_id = $(this).attr('data-buyer');
      var seller_id = $(this).attr('data-seller');
      var trans_id = $(this).attr('data-tr-id');
      var winner = $(this).attr('data-win');
      
      $("#confirm_ban").modal();
      $("#btn_confirm").attr({'data-buyer' : buyer_id, 'data-seller' : seller_id, 'data-win' : winner,'data-tr-id' : trans_id});
    });
  }

  function dispute_user()
  {
    $("#btn_confirm").click(function(){
      var buyer_id = $(this).attr('data-buyer');
      var seller_id = $(this).attr('data-seller');
      var trans_id = $(this).attr('data-tr-id');
      var winner = $(this).attr('data-win');

      var data = {'data_buyer' : buyer_id, 'data_seller' : seller_id, 'data_win' : winner,'data_trid' : trans_id}
      $.ajax({
        type : 'GET',
        url : "{{ url('dispute-user') }}",
        data : data,
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) 
        {
          if(result.err == 0)
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

  function notify_user()
  {
    $("body").on("click",".notify",function()
    {
      var trans_id = $(this).attr('data-tr-id');
      var user_id = $(this).attr('data-id');
      var role = $(this).attr('data-role');
      var data = {'trans_id':trans_id, 'user_id':user_id, 'role':role}; 

      /*console.log(data);
      return false;*/

      $.ajax({
        type : 'GET',
        url : "{{ url('dispute-notify') }}",
        dataType: 'json',
        data : data,
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) 
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
    });
  }

  function detail_user()
  {
    $("body").on("click",".detail",function()
    {
      var role = $(this).attr('role');
      var name = $(this).attr('data-name');
      var date_dispute = $(this).attr('date-dispute');
      var identity = $(this).attr('data-identity');
      var proof = $(this).attr('data-proof');
      var mutation = $(this).attr('data-mutation');

      if(role == 1)
      {
        $("#identity_popup").show();
        $("#mutation_popup").show();
        $("#role_popup").html('Pembeli');
      }
      else
      {
        $("#identity_popup").hide();
        $("#mutation_popup").hide();
        $("#role_popup").html('Penjual');
      }

      $("#name_popup").html(name);
      $("#date_dispute_popup").html(date_dispute);
      $("#identity_popup").attr('href',identity);
      $("#proof_popup").attr('href',proof);
      $("#mutation_popup").attr('href',mutation);
      $("#detail_user").modal();
    });
  }

  function display_table()
  {
      $.ajax({
        type : 'GET',
        url : "{{ url('dispute-list-admin') }}",
        dataType: 'html',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) 
        {
          $("#dispute_table").html(result);
        },
        complete : function()
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#dispute_list").DataTable({"aaSorting" : []});
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
      /**/
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