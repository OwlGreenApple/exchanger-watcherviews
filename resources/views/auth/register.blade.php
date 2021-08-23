@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    
                   <div id="div-register">
                      <form class="add-contact" id="form-register">
                          <div class="form-group">
                            <label>{{ $lang::get('custom.name') }}*</label>
                            <input type="text" name="username" class="form-control" placeholder="Input Your Name" required />
                            <span class="error username" role="alert"></span>                             
                          </div>

                          <div class="form-group">
                            <label>Email*</label>
                             <input id="email" type="email" class="form-control" name="email" required autocomplete="email" placeholder="Input Your Email">
                             <span class="error email"></span>
                          </div>

                          <div class="form-group">
                            <label>{{ $lang::get('custom.phone') }}* <span class="tooltipstered" title="<div class='panel-content'>
                                  {{ $lang::get('custom.intl') }}
                                </div>">
                                <i class="fa fa-question-circle "></i>
                              </span>
                            </label>
                            <input type="text" id="phone" name="phone" class="form-control" required/>
                            <span class="error phone"></span>

                            <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
                           <input name="data_country" type="hidden" /> 
                          </div>

                          <div class="form-group">
                            <label>Gender*</label>
                            <div>
                              <div class="form-check form-check-inline">
                                <label class="custom-radio">
                                  <input class="form-check-input" type="radio" name="gender" value="male" id="radio-male" checked>
                                  <span class="checkmark"></span>
                                </label>
                                <label class="form-check-label" for="radio-male">{{ $lang::get('custom.male') }}</label>
                              </div>

                              <div class="form-check form-check-inline">
                                <label class="custom-radio">
                                  <input class="form-check-input" type="radio" name="gender" id="radio-female" value="female">
                                  <span class="checkmark"></span>
                                </label>
                                <label class="form-check-label" for="radio-female">{{ $lang::get('custom.female') }}</label>
                              </div>

                            </div>
                            <!-- -->
                          </div>

                          <div class="form-group">
                              <label class="custom-checkbox">
                                  <input type="checkbox" name="agreement" required id="check-terms"/>
                                  <span class="checkmark-check"></span>
                              </label>
                              <label class="checkbox-left" for="check-terms"><sb class="ml-4">{{ $lang::get('custom.agreement') }}<a href="{{ env('APP_URL') }}terms-of-services/" target="_blank" style="text-decoration: underline;">{{ $lang::get('custom.terms') }}</a></sb></label>
                          </div>

                          <div class="text-left">
                            <button id="btn-register" type="button" class="btn btn-primary btn-lg">{{ $lang::get('custom.register') }}</button>
                          </div>
                          <input type="hidden" name="recaptcha_response" id="recaptchaResponse" readonly="readonly"/>
                      </form>
                    </div>
                </div> <!-- end card body -->
                <!-- end card -->
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT -->
<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">

  function registerAjax()
  {
    $("#btn-register").click(function(){
      var val= $("input[name=agreement]").val();

      if(val == 'on'){
        alert('Please Check Agreement Box');
        return false;
      }

      $.ajax({
        type: 'POST',
        url: "{{url('register')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form-register").serializeArray(),
        dataType: 'text',
        beforeSend: function() {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
        },
        success: function(result) {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            var data = jQuery.parseJSON(result);

            if (data.success == 1) 
            {
                $(".error").hide();
                $(".step-2").show();
                $("#step-1").html('<p>{{ $lang::get("custom.conf_order") }}</p><span class="sumo-psuedo-link">'+data.email+'</span>');
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