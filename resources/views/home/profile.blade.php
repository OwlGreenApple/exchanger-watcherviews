@extends('layouts.app')

@section('content')

<div class="bg-custom">
  <div class="container">
    <div class="col-md-12 py-4">
      <h2 class="mb-0"><b class="mr-2">Profil</b><i class="fas fa-user"></i></h2>  
    </div>
  </div>
</div>

<hr>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
                  <span id="server-error"><!-- error server --></span>
                  <form class="add-contact" id="form-register">

                    <div class="form-group form-inline">
                      <label>Email : </label>
                       <div class="form-control bg-custom text-white ml-2">{{ $user->email }}</div>
                    </div>

                    <div class="form-group form-inline">
                      <label>Membership : 
                         <span class="ml-1">@if($user->membership == null || $user->membership =="")  Free @else <div class="badge badge-success ml-1">{{ $user->membership }}</div> Expired : <b>{{ Date("d-M-Y",strtotime($user->valid_until)) }}</b> @endif
                         </span>
                      </label>
                    </div>

                    <div class="form-group">
                      <label>Nama</label>
                      <input type="text" name="username" class="form-control" placeholder="Input Your Name" required value="{{ $user->name }}" />
                      <span class="error username" role="alert"></span>                             
                    </div>

                    <div class="form-group">
                      <label>No Hp</label>
                       <div id="current_phone" class="form-control">{{ $user->phone }}</div>
                    </div>

                    <div class="form-group">
                      <label>Edit No Hp <span class="tooltipstered" title="<div class='panel-heading'>Message</div><div class='panel-content'>
                            Fill with your phone number without 0 or country code<br/>
                            For example : 8123456789, (201)5555555
                          </div>">
                          <i class="fa fa-question-circle "></i>
                        </span>
                      </label>
                      <input type="text" id="phone" name="phone" class="form-control" required/>
                      

                      <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
                      <input name="data_country" type="hidden" /> 
                      <span class="error code_country"></span>
                      <span class="error phone"></span>
                    </div>

                    <div class="form-group">
                      <label>Password Lama</label>
                       <input type="password" class="form-control" name="oldpass" placeholder="Old Password">
                       <span class="error oldpass"></span>
                    </div>

                    <div class="form-group">
                      <label>Password Baru</label>
                       <input type="password" class="form-control" name="password" placeholder="New Password">
                       <span class="error password"></span>
                    </div>

                    <div class="form-group">
                      <label>Konfirmasi Password Baru</label>
                       <input type="password" class="form-control" name="confpass" placeholder="Confirm New Password">
                       <span class="error confpass"></span>
                    </div>

                    <div class="text-left">
                      <button id="btn-register" type="button" class="btn btn-dark">Perbaharui</button>
                    </div>
                </form>
                <!-- end cardbody -->
                </div>

            </div>
        </div>
    </div>

<!-- Modal Popup Login -->
<div class="modal fade" id="popup_login" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
           Welcome ...
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        popup login
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
      
  </div>
</div>
<!-- end container -->
</div>

<script type="text/javascript" src="{{ asset('/assets/intl-tel-input/callback.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/intl-tel-input/js/custom-intl.js') }}"></script> 
<script type="text/javascript">

  var popup = "{{ Session::get('popup') }}";
  $(function()
  {
    updateProfile();
    display_popup();
  });

  function display_popup()
  {
    if(popup == 1)
    {
      $("#popup_login").modal();
    }
  }

  function updateProfile(){
    $("#btn-register").click(function()
    {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: "{{url('update-profile')}}",
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

          if (data.error == 0) 
          {
            $(".error").hide();
            $("#current_phone").text(data.current_phone);
            $("#server-error").html('<div class="alert alert-success">Your profile updated successfully.</div>');
            scrollTop();
          } 
          else if(data.error == 1)
          {
            $("#server-error").html('<div class="alert alert-danger">Sorry, our server is too busy, please try again later.</div>');
            scrollTop();
          }
          else 
          {
             $(".error").show();
             $(".username").html(data.username);
             $(".code_country").html(data.code_country);
             $(".phone").html(data.phone);
             $(".oldpass").html(data.oldpass);
             $(".confpass").html(data.confpass);
             $(".password").html(data.password);
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

</script>
@endsection