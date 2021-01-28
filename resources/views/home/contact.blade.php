@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Kontak Kami</h5></div>

                <div class="card-body">
                  <span id="server-message"><!-- error server --></span>
                  <form class="add-contact" id="form-register">

                    <div class="form-group">
                      <label>Judul*</label>
                      <input type="text" name="title" class="form-control" placeholder="Masukkan judul pesan" required />
                      <span class="error title"></span>                             
                    </div>

                    <div class="form-group">
                      <label>Pesan*</label>
                      <textarea maxlength="190" placeholder="Maximal 190 karakter" class="form-control" name="message" style="resize: none;"></textarea>
                      <span class="error message"></span>
                    </div>

                    <div class="text-left">
                      <button id="btn-send" type="button" class="btn btn-custom">Kirim</button>
                    </div>
                </form>
                <!-- end cardbody -->
                </div>

            </div>
        </div>
    </div>

    <!-- DATATABLE -->
    <div class="table-responsive mt-6"><div id="content"><!-- data message here --></div></div>
</div>

<!-- Modal User Message -->
<div class="modal fade" id="message_popup" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Pesan Anda
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div id="message_user"><!-- user's message here --></div>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-default" data-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- Modal User Reply -->
<div class="modal fade" id="reply_popup" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Balasan Dari Admin
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div id="message_reply"><!-- admin's message here --></div>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-default" data-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">
  $(function()
  {
    send_message();
    display_table();
    open_message();
    open_reply();
  });

  function open_reply(){
    $("body").on("click",".open_reply",function()
    {
      $("#message_reply").html($(this).attr('data-attr'));
      $("#reply_popup").modal();
    });
  }

  function open_message(){
    $("body").on("click",".open_message",function()
    {
      $("#message_user").html($(this).attr('data-attr'));
      $("#message_popup").modal();
    });
  }

  function send_message(){
    $("#btn-send").click(function()
    {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: "{{url('send-contact')}}",
        data: $("#form-register").serializeArray(),
        dataType: 'json',
        beforeSend: function() 
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(data) 
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(data.error == 0) 
          {
            $(".error").hide();
            $("#server-message").html('<div class="alert alert-success">Pesan anda sudah terikirim, admin kami akan segera membalas pesan anda.</div>');
            $("input, textarea").val('');
            scrollTop();
            display_table();
          } 
          else if(data.error == 1)
          {
            $("#server-message").html('<div class="alert alert-danger">Maaf server kami terlalu sibuk, silahkan coba lagi nanti.</div>');
            scrollTop();
          }
          else 
          {
             $(".error").show();
             $(".title").html(data.title);
             $(".message").html(data.message);
          }
          $(".alert").delay(5000).fadeOut(3000);
        },
        error: function(xhr,attr,throwable)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
      
    });
  }

  function scrollTop()
  {
    $('html, body').animate({
        scrollTop: $(".container").offset().top
    }, 1000);
  }

  function display_table()
  { 
    $.ajax({
      type : 'GET',
      url : "{{ url('contact-table') }}",
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
  }

</script>
@endsection