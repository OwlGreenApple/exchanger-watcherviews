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

    <div id="dispute_table" class="col-4 col-md-12 col-lg-12 table-responsive">
      <!-- display dispute -->
    </div>
  <!--  -->
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
    dispute_form();
    display_table()
    popup_new_window();
    dispute_user();
  });

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

  function dispute_form()
  {
    $("body").on("click",".blame",function(){
      var id_dispute = $(this).attr('data-id');
      $("#confirm_ban").modal();
      $("#btn_confirm").attr('data-id',id_dispute);
    });
  }

  function dispute_user(){
    $("#btn_confirm").click(function(){
      var id = $(this).attr('data-id');

      $.ajax({
        type : 'GET',
        url : "{{ url('dispute-user') }}",
        data : {id:id},
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