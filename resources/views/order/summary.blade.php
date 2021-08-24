@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('assets/css/summary.css')}}">

@if(env('APP_ENV') == 'production')
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>"></script>
<script>
  grecaptcha.ready(function() {
    grecaptcha.execute("<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>", {action: 'contact_form'}).then(function(token) {
        $('#recaptchaResponse').val(token);
    });
  });
</script>
@endif

<?php 
	$is_login = false;
	if (Auth::check()) {
		$is_login = true;
	}
?>
<section>
    <div class="container">
      
      @if (session('status'))
          <div class="alert alert-danger">
              {{ session('status') }}
          </div>
      @endif

      <div class="row sumo-split-layout">
              <div class="col-md-6 sumo-col-left pb-0 pb-md-50">
                <div class="mt-50 my-md-50 mr-lg-50 pr-md-30">
                  <div class="pb-20">
                    <h1 class="mb-0">Checkout</h1>
                  </div>
                  <a class="d-inline-block d-md-none pb-30" data-toggle="modal" data-target="#summaryModal">
                    <span class="d-flex align-items-center">
                      <img class="mr-10" src="https://appsumo2.b-cdn.net/static/images/svg/baseline-shopping_cart-24px.svg" width="auto" height="24">
                      <span class="sumo-psuedo-link">{{ $lang::get('custom.cart_summary') }}</span>
                    </span>
                  </a>
                  <div id="checkoutSteps">
                    <!-- Card 1 -->
                    <div class="checkout-step-container mb-30">
                      <div class="card checkout-card card-step-1 filled" id="cardStep1">
                      <div class="card-header">
                        <div class="d-flex align-items-center">
                          <h2 class="h3" id="header-step1">1. <?php if ($is_login) { ?>{{ $lang::get('custom.verify_account') }}<?php } else { ?>{{ $lang::get('custom.create_account') }} <?php } ?></h2>
                        </div>
                      </div>
                      <!-- End Card Header -->
                      <div class="card-body">
                        <span class="credential"><!--  --></span>
                        <!-- Card Data Summary -->
                        <div class="card-data-summary show" id="step-1">
              						<?php if ($is_login) { ?>
                          <p>{{ $lang::get('custom.conf_order') }}</p>
                          <span class="sumo-psuedo-link">{{Auth::user()->email}}</span>
              						<?php } else { ?>

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
                                      <label class="checkbox-left" for="check-terms"><sb>{{ $lang::get('custom.agreement') }}<a href="{{ env('APP_URL') }}terms-of-services/" target="_blank" style="text-decoration: underline;">{{ $lang::get('custom.terms') }}</a></sb></label>
                                  </div>

                                  <div class="text-left">
                                    <button id="btn-register" type="button" class="btn btn-primary btn-lg">{{ $lang::get('custom.register') }}</button>
                                  </div>
                                  <input type="hidden" name="recaptcha_response" id="recaptchaResponse" readonly="readonly"/>
                              </form>

                              <hr class="mt-5" />

                              <div class="mt-4 mb-3"><sb>{{ $lang::get('custom.have_account') }}<a href="" id="link-to-login"> {{ $lang::get('custom.login') }}</a></sb></div>
              							</div>
              							<div id="div-login" style="display:none;">
              								<form class="add-contact" method="POST" id="form-login">
              										@csrf
              										 <div class="form-group">
              												<label>Email*</label>
              												 <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }} {{ Cookie::get('email') }}" required autocomplete="email" placeholder="Input Your Email">

              													@error('email')
              															<span class="invalid-feedback" role="alert">
              																	<strong>{{ $message }}</strong>
              															</span>
              													@enderror
              											</div>

              											<div class="form-group">
              												<label>Password *</label>
              												 <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" value="{{ Cookie::get('password') }}" placeholder="Input Your Password">
              												
              												 @error('password')
              															<span class="invalid-feedback" role="alert">
              																	<strong>{{ $message }}</strong>
              															</span>
              												 @enderror
              											</div>

              											<div class="form-group">
              												<label class="custom-checkbox">
              														<input type="checkbox" name="remember"/ id="remember-login">
              														<span class="checkmark-check"></span>
              												</label>
              												<label class="checkbox-left" for="remember-login"><sb>{{ $lang::get('custom.remember') }}</sb></label>
              											</div>


              											<div class="text-left">
              												<button type="button" id="button-login" class="btn btn-primary btn-lg">{{ $lang::get('custom.log_in') }}</button>
              											</div>
              								</form>

              								<hr class="mt-5" />

              								<div class="mt-4 mb-3"><sb>{{ $lang::get('custom.need') }} ?<a href="" id="link-to-register"> {{ $lang::get('custom.reg') }}</a></sb></div>
              							</div>
              						<?php } ?>
                        </div>
                      </div>
                      </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="checkout-step-container">
                      <div class="card checkout-card" id="cardStep2">
                      <div class="card-header">
                        <div class="d-flex align-items-center">
                          <h2 class="h3">2. Order Review</h2>
                        </div>
                      </div>
                      <!-- End Card Header -->
                      <div class="card-body">
                        <!-- Card Data Entry -->
                        <div class="card-data-entry step-2" <?php if (!$is_login) { ?> style="display:none;"<?php } ?>> <!-- style="display: none;" -->
                          <p>
                            <b>
                             {{ $lang::get('custom.review') }}
                            </b>
                          </p>
                          <!-- Mobile Checkout Summary  -->
                          <div id="cart-checkout-summary-mobile" class="d-block d-md-none">

              <!-- Table -->
              <table class="table sumo-purchases-table">
                <tbody>
                  
                    <tr>
                      <td class="sumo-td-name">
                        <div class="sumo-title">
                          <b>{!! session('order')['package'] !!}</b>
                          
                        </div>
                      </td>
              				
                      <td class="sumo-td-price text-right sumo-checkout-item cart-item">
              					Rp. <?php echo number_format(session('order')['price'], 0, '', '.'); ?>
                      </td>
              				
                    </tr>
                  
                </tbody>
              </table>
              <!-- End Table -->
              <div>
                  <div class="as-checkout-entry" id="checkout-total" data-total="79.00">
                    <strong class="as-checkout-total">Total</strong>
                    <strong class="as-checkout-total-price total_price_default" id="totalprice_sidebar totalprice_mobile">
              				Rp. <?php echo number_format(session('order')['price'], 0, '', '.'); ?>
              			</strong>
                  </div>
              </div>
            <!--  -->
            </div>
            <!-- Checkout Button Container -->
            <div class="checkout-button-container mt-30 step-2" id="checkout-buttons-2" <?php if (!$is_login) { ?> style="display:none;"<?php } ?>>
							
              <div class="sumo-product-note light mt-20">
                {{ $lang::get('custom.accept') }}<a href="{{ env('APP_URL') }}terms-of-services/" target="_blank">{{ $lang::get('custom.terms') }}</a>, {{ $lang::get('custom.and') }} <a href="{{ env('APP_URL') }}privacy-policy/" target="_blank" style="text-decoration: underline;">{{ $lang::get('custom.policy') }}</a>.
              </div>
            </div>
            <!-- End Mobile Checkout Summary -->
            <hr class="my-30">
            <h4 class="mb-10"><b>{{ $lang::get('custom.need_help') }}</b></h4>
            <p>{{ $lang::get('custom.support_team') }}</p>
              
            <a href="whatsapp://send/?phone=+62817318368" target="_blank" class="btn btn-more full-width-mobile waves-effect waves-light">{{ $lang::get('custom.find_help') }}</a>
          </div>
          <!-- End Card Data Entry -->
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

        
        <!-- Right Column -->
        <div class="col-md-6 sumo-col-right pb-50">
          <div class="mt-md-50 mb-50 ml-lg-50 pl-md-30">
            <div class="pb-20 d-md-block">
              <div class="placeholder"></div>
            </div>
            <!-- Summary -->
            <div class="card checkout-card checkout-summary dark mb-30 d-md-flex">
              <div class="card-header">
                <div class="d-flex align-items-center">
                  <h2 class="h3">Summary</h2>
                  <a class="edit-link ml-auto" href="<?php echo url()->previous(); ?>">Edit</a>
                </div>
              </div>
              <!-- End Card Header -->
              <div class="card-body pt-20">

<div id="cart-checkout-summary">
  <!-- Desktop Table -->
  <!-- Table -->
  <table class="table sumo-purchases-table">
    <tbody>
      
        <tr>
          <td class="sumo-td-name">
            <div class="sumo-title">
              {{ $lang::get('custom.order') }} <b class="text-capitalize">{!! session('order')['package'] !!}</b>
            </div>
          </td>
        </tr>
        <!-- <tr>
          <td class="sumo-td-price sumo-checkout-item" data-item-id="2101">
           
          </td>
        </tr> -->
      
    </tbody>
  </table>
  <!-- End Table -->
  <div>

  <form method="POST" action="{{url('submit_payment')}}">
    <div class="as-checkout-entry" id="checkout-total">
      <div class="col-md-12 col-12">
        <strong class="as-checkout-total">Total : </strong>
        <strong class="as-checkout-total-price total_price" id="totalprice_sidebar totalprice_mobile"><b>
        Rp. <?php echo number_format(session('order')['total'], 0, '', '.'); ?></b>
  			</strong>
      </div>
    </div>
</div>
  
</div>
									
										{{ csrf_field() }}
                    <div class="checkout-button-container mt-30 step-2" id="checkout-buttons-1" <?php if (!$is_login) { ?> style="display:none;"<?php } ?>>

                        <input type="hidden" name="summary" value="true"/>
												<input type="submit" name="submit" id="submit" class="col-md-12 col-12 btn btn-primary bsub btn-block" value="Order Now"/>
                      <div class="sumo-product-note light mt-20">
                        {{ $lang::get('custom.accept') }}<a href="{{env('APP_URL')}}terms-of-services/" target="_blank">{{ $lang::get('custom.terms') }}</a>, {{ $lang::get('custom.and') }} <a href="{{env('APP_URL')}}privacy-policy/" target="_blank">{{ $lang::get('custom.policy') }}</a>.
                      </div>
                    </div>
									</form>
              <!-- Close Desktop Table -->
              </div>
            </div>
            <!-- Supplementary Info -->
            <div class="sumo-cart-supplement mt-30">
              <p class="sumo-cart-supplement-header">Hustle with Confidence</p>
              <ul class="list-inline mt-20">
                
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/calendar.svg">
                  <span style="font-style: italic;">Award winning developer, use our applications with ease in mind. We spent countless hours working hard to develop the best software & we committed to make it better each day.</span>
                </li>
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/lifebuoy.svg">
                  <span style="font-style: italic;">Preferred customer support. We take pride in going above and beyond to solve issues and keep our customers happy.<br>Email or simply chat with our Customer support.</span>
                </li>
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/message-text.svg">
                  <span style="font-style: italic;">We give best deal and savings on pricing packages everytime, on top of that make sure you checked our Coupon page every now and then, to get more discounts & promotions.</span>
                </li>
              </ul>
            </div>
            <!-- End Supplemetary Info -->
          </div>
        </div>
      </div>
    </div>
  </section>

<script src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  function getUpgrade()
  {
    $("input[name='status_upgrade']").change(function(){
      var val = $(this).val();

      $.ajax({
        type: 'GET',
        url: "{{ url('get-status-upgrade') }}",
        data: {
          'status_upgrade':val,
        },
        dataType: 'json',
        beforeSend: function() 
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(data) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".total_price").html('Rp '+'<strike>'+formatNumber(data.price)+'</strike> '+formatNumber(data.total))
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

	function formatNumber(num) {
    if(isNaN(num) == false)
    {
      return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
		else
    {
      return '';
    }
	}

  function rememberMe(){
    $("input[name=remember]").click(function(){
      var val = $(this).val();

      if(val == 1){
        $(this).val('on');
      }
      else {
        $(this).val(1);
      }

    });
  }

	function loginAjax(){
    $(".upgrade").hide();
    $("body").on("click", "#button-login", function() {
     
			$.ajax({
				type: 'POST',
				url: "{{ url('loginajax') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: $("#form-login").serializeArray(),
				dataType: 'json',
				beforeSend: function() {
					$('#loader').show();
					$('.div-loading').addClass('background-load');
				},
				success: function(data) {
					$('#loader').hide();
					$('.div-loading').removeClass('background-load');

					if(data.success === 1) {
						$(".step-2").show();
            $(".bsub").show();
						$("#step-1").html('<p>{{ $lang::get("custom.conf_order") }}</p><span class="sumo-psuedo-link">'+data.email+'</span>');
            $(".credential").html('');
					} 
					else 
          {
						$(".credential").html('<div class="alert alert-danger">'+data.message+'</div>');
					}
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

	function registerAjax(){
    $("#btn-register").click(function(){
      var val= $("input[name=agreement]").val();

      if(val == 'on'){
        alert('{{ $lang::get("custom.check") }}');
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
					else {
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
			
			
    });
	}
	
	function initButton(){
    $("body").on("click", "#link-to-login", function(e) {
			e.preventDefault();
      $("#div-login").show();
      $("#div-register").hide();
      $("#header-step1").html("1. Login");
			$('html, body').animate({scrollTop: '0px'}, 300);
    });
    $("body").on("click", "#link-to-register", function(e) {
			e.preventDefault();
      $("#div-login").hide();
      $("#div-register").show();
      $("#header-step1").html("1. Create an account");
			$('html, body').animate({scrollTop: '0px'}, 300);
    });
	}
	
	function checkField(){
		if ($("#email").val()!="" && $("#phone").val()!="" && $("#username").val()!="" && $("#check-terms").val()==1) {
			$("#btn-register").addClass("register-active");
		}
	}

	function onChangeRegister(){
		$("#email,#phone,#username,#check-terms").change(function(){
			checkField();
		});
	}

  $(document).ready(function() {
		rememberMe();
		
		loginAjax();
		registerAjax();
    // getUpgrade();

		initButton();
		onChangeRegister();
  });
    
</script>

@if(!$is_login)
  <script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
@endif
@endsection