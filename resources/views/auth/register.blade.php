@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                  <form class="add-contact" id="form-register">
                   
                    @if($ref_name !== null)
                    <div class="form-group">
                      <label>Referral</label>
                      <div class="form-control">{{ $ref_name }}</div>    
                      <span class="error referral" role="alert"></span>         
                    </div>
                    @endif
                   
                    <div class="form-group">
                      <label>Name*</label>
                      <input type="text" name="username" class="form-control" placeholder="Input Your Name" required />
                      <span class="error username" role="alert"></span>                             
                    </div>

                    <div class="form-group">
                      <label>Email*</label>
                       <input id="email" type="email" class="form-control" name="email" required autocomplete="email" placeholder="Input Your Email">
                       <span class="error email"></span>
                    </div>

                    <div class="form-group">
                      <label>Phone* <span class="tooltipstered" title="<div class='panel-heading'>Message</div><div class='panel-content'>
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
                      <label>Gender*</label>
                      <div>
                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="gender" value="male" id="radio-male" checked>
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="radio-male">Male</label>
                        </div>

                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="gender" id="radio-female" value="female">
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="radio-female">Female</label>
                        </div>

                      </div>
                      <!-- -->
                    </div>

                    <div class="form-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="agreement" required id="check-terms"/>
                            <span class="checkmark-check"></span>
                        </label>
                        <label class="checkbox-left" for="check-terms"><sb>I Agree with <a href="/terms-of-services/" target="_blank" style="text-decoration: underline;">Terms and Condition</a></sb></label>
                    </div>

                    <div class="text-left">
                      <button id="btn-register" type="button" class="btn btn-custom btn-lg">REGISTER</button>
                    </div>
                </form>
                <!-- end cardbody -->
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('/assets/intl-tel-input/callback.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/intl-tel-input/js/custom-intl.js') }}"></script> 
<script type="text/javascript">
  $(function()
  {
    agreement();
    registerAjax();
  });

  function agreement(){
    $("input[name=agreement]").click(function(){
      var val = $(this).val();

      if(val == 1){
        $(this).val('on');
      }
      else {
        $(this).val(1);
      }

    });
  }

  function registerAjax(){
    $("#btn-register").click(function(){
      var val= $("input[name=agreement]").val();

      if(val == 'on'){
        alert('Please Check Agreement Box');
        return false;
      }

      var data = $("#form-register").serializeArray();
      data.push({'name':'referral','value': "{{ $ref_id }}" });

      $.ajax({
        type: 'POST',
        url: "{{url('register')}}",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(data) {
          if (data.success == 1) 
          {
            $(".error").hide();
            location.href="{{url('home')}}"
          } 
          else {
             $('#loader').hide();
             $('.div-loading').removeClass('background-load');
             $(".error").show();
             $(".username").html(data.username);
             $(".email").html(data.email);
             $(".code_country").html(data.code_country);
             $(".phone").html(data.phone);
             $(".referral").html(data.referral);
          }
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
</script>
@endsection