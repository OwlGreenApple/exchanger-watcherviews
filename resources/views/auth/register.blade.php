@extends('layouts.auth')

@section('content')
                <h4>Masih Baru?</h4>
                <h6 class="font-weight-light">Mari bergabung dengan isi form berikut ini :</h6>
                <form class="pt-3 add-contact" id="form-register">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="username" placeholder="Input Your Name" name="username">
                    <span class="error username"><!--  --></span>
                  </div>

                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg" id="email" placeholder="Email" required name="email">
                    <span class="error email"><!--  --></span>
                  </div>

                   <div class="form-group">
                      <input type="text" id="phone" name="phone" class="form-control" required/>
                      <span class="error phone"></span>

                      <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
                     <input name="data_country" type="hidden" /> 
                     <span class="error code_country"><!--  --></span>
                    </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Gender*</label>
                    <div class="col-sm-4">
                      <div class="form-check">
                        <label class="form-check-label">
                          <input type="radio" class="form-check-input" name="gender" id="membershipRadios1" value="" checked=""> {{ $lang::get('custom.male') }} <i class="input-helper"></i></label>
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <div class="form-check">
                        <label class="form-check-label">
                          <input type="radio" class="form-check-input" name="gender" id="membershipRadios2" value="option2"> {{ $lang::get('custom.female') }} <i class="input-helper"></i></label>
                      </div>
                    </div>
                  </div>
                  <div class="mb-4">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" name="agreement" required id="check-terms" class="form-check-input" />{{ Lang::get('custom.agreement') }} 
                      </label><a href="https://watchermarket.com/syarat-ketentuan/">Klik Disini</a>
                    </div>
                  </div>
                  <div class="mt-3">
                    <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" id="btn-register" type="button" >{{ $lang::get('custom.register') }}</button>
                  </div>
                  <div class="text-center mt-4 font-weight-light"> {{ $lang::get('custom.have_account') }} <a href="{{ url('login')}}" class="text-primary">{{ $lang::get('custom.log_in') }}</a>
                  </div>
                </form>
    
<!-- JAVASCRIPT -->
<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">

var bool = false;
$(function(){
    registerAjax();
});

  function registerAjax()
  {
    $("#btn-register").click(function(){
      var val= $("input[name=agreement]").val();

      if(val == 'on'){
        alert('{{ $lang::get("custom.check") }}');
        return false;
      }

      $.ajax({
        type: 'POST',
        url: "{{url('offer')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form-register").serializeArray(),
        dataType: 'json',
        beforeSend: function() {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
        },
        success: function(data) {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            if (data.success == 1) 
            {
                $(".error").hide();
                bool = true;
            } 
            else 
            {
                 $(".error").show();
                 $(".username").html(data.username);
                 $(".email").html(data.email);
                 $(".code_country").html(data.code_country);
                 $(".phone").html(data.phone);
            }
        },
        complete : function(xhr)
        {
           if(bool == true)
           {
             location.href="{{ url('/') }}";
           }
        },
        error: function(xhr,attr,throwable)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    /**/
    });
 }
 </script>
@endsection
