@extends('layouts.app')
<link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/order.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/account.css') }}" rel="stylesheet" />

@section('content')
    <div class="page-header">
      <h3 class="page-title">
        Account </h3>
    </div>
    <div class="row mt-3">

        <!-- LEFT TAB -->
        <div class="col-md-3 mb-4">
             <div class="card">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>Setelan</b></h5></div>
                <div class="font-weight-bold">
                    <a class="settings text-black-50 border-bottom mn_1 active" data_target="1"><i class="far fa-user text-primary"></i>&nbsp;Profile</a>

                    <!-- non admin && non suspended user -->
                    @if(Auth::user()->is_admin == 0 && Auth::user()->status !== 3)
                    <a class="text-black-50 d-block border-bottom mn" data-toggle="collapse" href="#collapseExample"><i class="fas fa-receipt text-warning"></i>&nbsp;Billing&nbsp;<i align="right" class="fas fa-caret-down float-right mt-1"></i></a>
                        <span class="clearfix"><!--  --></span>

                    <div class="border-bottom collapse px-2" id="collapseExample">
                           <a class="settings text-black-50" data_target="2">Upgrade</a>
                           <a class="settings text-black-50" data_target="3">Invoice</a>
                    </div>
                    <!--  -->
                    
                    <a class="settings text-black-50 border-bottom mn_4" data_target="4"><i class="fas fa-plug text-danger"></i>&nbsp;Connect API</a>

                    <a class="settings text-black-50 border-bottom mn_5" data_target="5"><i class="fas fa-exchange-alt text-success"></i>&nbsp;Tukar Coin</a>
                    @endif

                    <a target="_blank" rel="noopener noreferrer" href="https://play.google.com/store/apps/dev?id=5168871764586057901" class="settings text-black-50 border-bottom mn_1" data_target="1">Watcherviews</a>

                    <a class="settings text-info border-bottom" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();"><i class="mdi mdi-logout text-primary"></i>&nbsp;Log Out&nbsp;</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    
                </div>
            </div>
        </div>

        <!-- RIGHT TAB -->
        <div class="col-md-9">
            <!-- PROFILE -->
            <div id="settings_target_1" class="card target_hide">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b><i class="far fa-user text-success"></i>&nbsp;Profile</b></h5></div>

                <div class="card-body">
                    <div class="msg"><!--  --></div>
                    @include('home.profile')
                </div>
            </div>

            @if(Auth::user()->status !== 3 && Auth::user()->is_admin == 0)
            <!-- UPGRADE PACKAGE -->
            <div id="settings_target_2" class="card target_hide d-none">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>Upgrade</b></h5></div>

                <div class="card-body">
                    <div class="msg"><!--  --></div>
                    @include('package-list')
                </div>
            </div>

            <!-- INVOICE -->
            <div id="settings_target_3" class="card target_hide d-none">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>Invoice</b></h5></div>

                <div class="card-body">
                    <div class="msg"><!--  --></div>
                    @include('home.order')
                </div>
            </div>

            <!-- CONNECT API -->
            <div id="settings_target_4" class="card target_hide d-none">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>{{ Lang::get('custom.api') }}</b></h5></div>

                <div id="connect_api_gui" class="card-body">
                    <span class="wallet mb-2"><!--  --></span>
                    <div class="msg"><!--  --></div>
                    @if($user->watcherviews_id > 0)
                        <div class="alert alert-info">{{ Lang::get('auth.api') }} : <u><a id="logout_watcherviews">Disconnect API</a></u></div>

                        <div class="mb-2"><a class="btn btn-gradient-success" href="{{ url('buy') }}">Beli Coin</a></div>

                        <div class="mb-2"><a class="btn btn-gradient-warning text-dark" href="{{ url('wallet') }}">Jual Coin</a></div>

                        <div><a target="_blank" rel="noopener noreferrer" class="btn btn-gradient-danger text-white" href="https://play.google.com/store/apps/dev?id=5168871764586057901">Download Watcherviews</a></div>
                    @else
                        @include('home.connect_api')
                    @endif
                </div>
            </div>
            @endif

            <!-- EXCHANGE COIN -->
            <div id="settings_target_5" class="card target_hide d-none">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>Tukar Coin dengan voucher kupon</b></h5></div>

                <div class="card-body">
                    @include('exchange')
                </div>
            </div>

            <!-- end col -->
        </div> 
    </div> 
    
    <!-- end justify -->

<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script defer src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    var segment = "{{ $conf }}";

    $(document).ready(function(){
        crop();
        data_tabs();
        save_profile();
        connect_api();
        logout_watcherviews();
        delete_payment();
        add_payment();
        display_detail_payment();
        save_bank_method();
        popup_payment();
        payment_tooltip();
        redeem_coin();
        copy_coupon();
    });

    function redeem_coin()
    {
        $(".open").click(function(){
            var id = $(this).attr('id');
            $(".exc").attr('id',id);
            $("#modal_exc").modal();
        });

        // EXECUTE
        $("body").on("click",".exc",function()
        {
            var data;
            var diskon_value;
            var id = $(this).attr('id');
           
            if(id == 'o_exc')
            {
                diskon_value = $("input[name='o_exchange']:checked").val();
                data = {"api":"omn","diskon_value":diskon_value};
            }
            
            if(id == 'a_exc')
            {
                diskon_value = $("input[name='a_exchange']:checked").val();
                data = {"api":"act","diskon_value":diskon_value};
            }

            if(id == 'atm_exc')
            {
                diskon_value = $("input[name='atm_exchange']:checked").val();
                data = {"api":"atm","diskon_value":diskon_value};
            }

            exchange_coin(data)
        });
    }

    function exchange_coin(data)
    {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type : 'POST',
            url : "{{ route('exc') }}",
            dataType : 'json',
            data : data,
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                //validation coin
                if(result.api !== undefined)
                {
                    if(result.api == 'omn')
                    {
                        $(".omn_coin").html('<div class="text-danger mt-2">{{ Lang::get("transaction.total.incoin") }}</div>');
                    }

                    if(result.api == 'act')
                    {
                        $(".act_coin").html('<div class="text-danger mt-2">{{ Lang::get("transaction.total.incoin") }}</div>');
                    }

                    if(result.api == 'atm')
                    {
                        $(".atm_coin").html('<div class="text-danger mt-2">{{ Lang::get("transaction.total.incoin") }}</div>');
                    }

                    return false;
                }

                // omnilinks
                if(result.coupon !== undefined)
                {
                    $("#omn_coupon").val(result.coupon);
                    $(".omn_coupon").show();
                }
                else
                {
                    if(result.coupon == 0)
                    {
                        $(".omn_coupon").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                    }
                }

                // activrespon
                if(result.act_coupon == 0)
                {
                    $(".act_coupon").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                }
                else 
                {
                    if(result.act_coupon !== undefined)
                    {
                        $("#act_coupon").val(result.act_coupon);
                        $(".act_coupon").show();
                    }
                }

                // activetemplate
                if(result.atm_coupon == 0)
                {
                    $(".atm_coupon").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                }
                else 
                {
                    if(result.atm_coupon !== undefined)
                    {
                        $("#atm_coupon").val(result.atm_coupon);
                        $(".atm_coupon").show();
                    }
                }

                if(result.coin !== 0)
                {
                    $("#cur_coin").html(result.coin)
                }
            },
            complete : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function copy_coupon(){
      $( "body" ).on("click",".btn-copy",function(e) 
      {
        e.preventDefault();
        e.stopPropagation();

        var code = $(this).attr("data-code");
        var link = $("#"+code).val();
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $(".display_"+code).html('<div class="text-success mt-2">Code kupon telah di salin</div>');
        setTimeout(function(){$(".display_"+code).html("")},3000)
      });
    }

     function load_page()
      {
        $("#data_order").DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [ 10, 25, 50, 75, 100, 500 ],
            "ajax": "{{ url('orders') }}",
            "destroy": true
        });

        $('.dataTables_filter input')
         .off()
         .on('keyup', delay(function() {
            $('#data_order').DataTable().search(this.value.trim(), false, false).draw();
         },1000));    
    }

    function payment_tooltip()
    {
        $(".pay-notes").tooltip({
            title : 'Silahkan screenshot qr-code payment anda dan upload di form ini'
        });
    }

    function add_payment()
    {
        $("#add-payment").click(function(){
            $("#dropdown-payment").show();
        });

        $("select[name='mpayment']").change(function(){
            var val = $(this).val();
            var method = $("option:selected").attr('method');

            if(val == 'bank')
            {
                $("#bank-payment").show();
                $("#e-payment").hide();
                $("#save-bank").attr('method',method);
            }
            else if(val == 'epay')
            {
                $("#bank-payment").hide();
                $("#e-payment").show();
            }
            else
            {
                $("#bank-payment, #e-payment").hide();
            }
        });
    }

    function save_bank_method()
    {
        $("#save-bank").click(function()
        {   
            var name = $("input[name='bank_name']").val();
            var no = $("input[name='bank_no']").val();
            var owner = $("input[name='bank_customer']").val();
            var method = $(this).attr('method');

            var data = {'bank_name':name,'bank_no':no,'bank_customer':owner,'method':method};

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('save-bank-payment') }}",
                dataType : 'json',
                data : data,
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    if(result.status == 'error')
                    {
                        $(".error").show();
                        $(".bank_name").html(result.bank_name);
                        $(".bank_customer").html(result.bank_customer);
                        $(".bank_no").html(result.bank_no);
                    }
                    else if(result.status == 0)
                    {
                        $("#err_profile").html('<div class="alert alert-danger">'+result.msg+'</div>');
                    }
                    else
                    {
                        $(".error").hide();
                        $("#err_profile").html('<div class="alert alert-success">'+result.msg+'</div>');

                        var el = '<div class="mb-2"><button data-value="'+result.bank[3]+'" data-name="'+result.bank[0]+'" data-no="'+result.bank[1]+'" data-owner="'+result.bank[2]+'" type="button" class="btn btn-info text-capitalize b_payment w-100"><span class="text-uppercase">'+result.bank[0]+'</span></button>';

                        if(result.bank[3] == 'bank_1')
                        {
                            $("#bank_1_method").html(el);
                        }
                        else
                        {
                            $("#bank_2_method").html(el);
                        }

                        $(".alert-danger").hide();
                        $("select[name='mpayment'] option:first").prop('selected',true);   
                        $("#bank-payment, #dropdown-payment").hide(); 
                    }
                },
                complete : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $(".msg").delay(3000).fadeOut(1000);
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function display_detail_payment()
    {
        $("body").on("click",".b_payment",function()
        {
            var name = $(this).attr('data-name');
            var no = $(this).attr('data-no');
            var owner = $(this).attr('data-owner');
            var method = $(this).attr('data-value');

            $("input[name='bank_name']").val(name);
            $("input[name='bank_no']").val(no);
            $("input[name='bank_customer']").val(owner);
            $("#save-bank").attr('method',method);
            $("#bank-del").attr('data-value',method).show();
            $("#bank-payment").show();
        });
    }

    function delete_payment()
    {
        $("body").on("click",".delpay",function()
        {
            var payment = $(this).attr('data-value');
            $("#confirm_payment_delete").modal();
            $("#btn_payment_delete").attr('data-value',payment);
        });
            

        $("body").on("click","#btn_payment_delete",function()
        {
            var payment = $(this).attr('data-value');
            $.ajax({
                type : 'GET',
                url : "{{ url('delete-payment') }}",
                dataType : 'json',
                data : {'payment':payment},
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   $(".error").hide();
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                        if(payment == 'bank_1')
                        {
                            $("#bank_1_method").html('');
                        }
                        else if(payment == 'bank_2')
                        {
                            $("#bank_2_method").html('');
                        }
                        else if(payment == 'epayment_1')
                        {
                            $("#display_ovo").html('');
                        }
                        else if(payment == 'epayment_2')
                        {
                            $("#display_dana").html('');
                        }
                        else
                        {
                            $("#display_gopay").html('');
                        }

                        $(".alert-danger").hide();
                        $("select[name='mpayment'] option:first").prop('selected',true);
                        $("#bank-payment, #dropdown-payment, #bank-del").hide(); 
                    }
                    else if(result.err == 2)
                    {
                        $("#crop_save").html('<div class="alert alert-danger">{{ Lang::get("custom.payment"); }}</div>');
                    }
                    else
                    {
                        $("#crop_save").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                    }
                },
                complete : function()
                {
                    $(".msg").delay(3000).fadeOut(1000);
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function crop()
    {
        // TO REMOVE FILE WGHEN USER PRESS CANCEL
        $(".crop_cancel").click(function(){
            $("input[name='payment']").val('');
        });

        var success = false;
        var $modal = $('#modal');
        var image = document.getElementById('sample_image');
        var cropper;

        $('.upload_payment').change(function(event){
            var files = event.target.files;

            var done = function(url){
                image.src = url;
                $modal.modal('show');
            };

            if(files && files.length > 0)
            {
                reader = new FileReader();
                reader.onload = function(event)
                {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });

        $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            preview:'.preview'
        });
        }).on('hidden.bs.modal', function(){
            cropper.destroy();
            cropper = null;
        });

        $('#crop').click(function(){
            canvas = cropper.getCroppedCanvas({
                width:150,
                height:150
            });

            canvas.toBlob(function(blob){
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function(){
                    var base64data = reader.result;
                    var epayment = $("select[name='epayment'] option:selected").val();
                    var epayname = $("input[name='epayname']").val();

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        method:'POST',
                        url:'{{ url("payment-upload") }}',
                        data:{'image':base64data,'epayment':epayment,'epayname':epayname},
                        success:function(data)
                        {
                            $modal.modal('hide');
                            if(data.pay == 0)
                            {
                                $("#crop_save").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                            }
                            else if(data.pay == 1)
                            {
                                $(".epayname").html('<span class="text-danger">'+data.epayname+'</span>');
                            }
                            else
                            {
                                var el = '<div class="mb-2"><button data-value="'+data.pay+'" type="button" class="btn btn-info delpay text-capitalize w-100">{{ Lang::get("custom.del") }} <span class="text-uppercase">'+data.epayname+'</span></button></div>';

                                if(data.pay == 'epayment_1')
                                { 
                                    // $('#ovo_image').attr('src', data.img);
                                    $("#display_ovo").html(el);
                                }
                                else if(data.pay == 'epayment_2')
                                {
                                    // $('#dana_image').attr('src', data.img);
                                    $("#display_dana").html(el);
                                }
                                else
                                {
                                    // $('#gopay_image').attr('src', data.img);
                                    $("#display_gopay").html(el);
                                }

                                $("#crop_save").html('<div class="alert alert-success">{{ Lang::get("custom.success") }}</div>')
                                success = true;
                            }
                        },
                        complete : function()
                        {
                            if(success == true)
                            {
                                $("select[name='mpayment'] option:first").prop('selected',true);
                                $("#e-payment, #dropdown-payment").hide(); 
                            }
                        }
                    });
                };
            });
        });

    /*end fun*/
    }
    
    function data_tabs()
    {
        if(segment == 1)
        {
           open_billing(3);
        }

        if(segment == 'wallet')
        {
            $(".target_hide").addClass('d-none');
            $(".settings").removeClass('active');
            $("#settings_target_4").removeClass('d-none');
            $(".mn_4").addClass('active');
        }

        if(segment == 'membership')
        {
            open_billing(2);
        }

        $(".settings").click(function(){
            var target = $(this).attr('data_target');
            $(".settings").removeClass('active');
            $(".target_hide").addClass('d-none');
            $("#settings_target_"+target).removeClass('d-none');
            if(target == 2 || target == 3)
            {
                $(".mn").addClass('active');
            }
            else
            {
                $(".mn").removeClass('active');
                $(".mn_"+target).addClass('active');
            }

            if(target == 3)
            {
                load_page();
            }
        });
    }

    function open_billing(page)
    {
        $(".target_hide").addClass('d-none');
        $(".settings").removeClass('active');
        $("#settings_target_"+page).removeClass('d-none');
        $(".mn").addClass('active');
        $(".mn").removeClass('collapsed');
        $("#collapseExample").removeClass('collapse');
        $("#collapseExample").addClass('collapse show');
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

    function save_profile()
    {
        $("#profile").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('update-profile') }}",
                dataType : 'json',
                data : $(this).serialize(),
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   $(".error").hide();
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.status == 'error')
                    {
                        $(".error").show();
                        $(".name").html(result.name);
                        $(".phone").html(result.phone);
                        $(".phone").html(result.code_country); //exceptional
                        $(".oldpass").html(result.oldpass);
                        $(".confpass").html(result.confpass);
                        $(".newpass").html(result.newpass);
                    }
                    else
                    {
                        $("#phone_number").html(result.phone);
                        $("#err_profile").html('<div class="alert alert-success">'+result.msg+'</div>');
                    }
                },
                complete : function()
                {
                    $(".msg").delay(3000).fadeOut(1000);
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function connect_api()
    {
        $("#connect_api").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('connect-api') }}",
                dataType : 'json',
                data : $(this).serialize(),
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   $(".error").hide();
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                        $("#connect_api_gui").html('<div class="alert alert-info">{{ Lang::get("auth.api") }} : <b><a id="logout_watcherviews">Disconnect API</a></div><div class="mb-2"><a class="btn btn-gradient-success" href="{{ url("buy") }}">Beli Coin</a></div><div class="mb-2"><a class="btn btn-gradient-warning text-dark" href="{{ url("wallet") }}">Jual Coin</a></div><div><a target="_blank" rel="noopener noreferrer" class="btn btn-gradient-danger text-white" href="https://play.google.com/store/apps/dev?id=5168871764586057901">Download Watcherviews</a></div>')
                        $("input").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("auth.credential") }}</div>');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                    }
                    else if(result.err == 3)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("auth.api_registered") }}</div>');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                    else
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                },
                complete : function()
                {
                    $(".msg").delay(3000).fadeOut(1000);
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function logout_watcherviews()
    {
        $("body").on("click","#logout_watcherviews",function(){
            logout('logout-watcherviews');
        });
    }

    function logout(url)
    {
         $.ajax({
            type : 'GET',
            url : "{{ url('') }}/"+url,
            dataType : 'json',
            data : $(this).serialize(),
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.err == 0)
                {
                    location.href="{{ url('account') }}/wallet";
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $(".wallet").html('<div class="alert alert-danger">'+result.err+'</div>');
                }
            },
            error: function(){
               $('#loader').hide();
               $('.div-loading').removeClass('background-load');
            }
        });
    }

    function popup_payment()
    {
      $( "body" ).on( "click", ".popup-newWindow", function()
      {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
      });
    }

  /**/

  $( "body" ).on( "click", ".view-details", function() {
    var id = $(this).attr('data-id');
    $('.details-'+id).toggleClass('d-none');
  });
  
  $( "body" ).on( "click", ".btn-search", function() {
    currentPage = '';
    refresh_page();
  });

  $( "body" ).on( "click", ".btn-confirm", function() {
    $('#id_confirm').val($(this).attr('data-id'));
    $('#mod-no_order').html($(this).attr('data-no-order'));
    $('#mod-package').html($(this).attr('data-package'));

    var total = parseInt($(this).attr('data-total'));
    $('#mod-total').html('Rp. ' + total.toLocaleString());
    $('#mod-purchased_view').html(parseInt($(this).attr('data-purchased-view')).toLocaleString()); 
   
    $('#mod-date').html($(this).attr('data-date'));

    var keterangan = '-';
   // console.log($(this).attr('data-keterangan'));
    if($(this).attr('data-keterangan')!='' || $(this).attr('data-keterangan')!=null){
      keterangan = $(this).attr('data-keterangan');
    }

    $('#mod-keterangan').html(keterangan);
  });

  $( "body" ).on( "click", ".btn-delete", function() {
    $('#id_delete').val($(this).attr('data-id'));
  });

  $( "body" ).on( "click", "#btn-delete-ok", function() {
    delete_order();
  });

  $(document).on('click', '.checkAll', function (e) {
    $('input:checkbox').not(this).prop('checked', this.checked);
  });

</script>
@endsection
