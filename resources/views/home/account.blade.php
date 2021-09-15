@extends('layouts.app')
<link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/order.css') }}" rel="stylesheet" />

@section('content')
<div class="container">
    <h2><b>Akun</b></h2>  
    <div class="row justify-content-center mt-3">

        <!-- LEFT TAB -->
        <div class="col-md-3">
             <div class="card">
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>Setelan</b></h5></div>
                <div class="font-weight-bold">
                    <a class="settings text-black-50 border-bottom mn_1 active" data_target="1"><i class="far fa-user text-primary"></i>&nbsp;Profile</a>

                    @if(Auth::user()->is_admin == 0)
                    <a class="text-black-50 d-block border-bottom mn" data-toggle="collapse" href="#collapseExample"><i class="fas fa-receipt text-warning"></i>&nbsp;Billing&nbsp;<i align="right" class="fas fa-caret-down float-right mt-1"></i></a>
                        <span class="clearfix"><!--  --></span>

                    <div class="border-bottom collapse px-2" id="collapseExample">
                           <a class="settings text-black-50" data_target="2">Upgrade</a>
                           <a class="settings text-black-50" data_target="3">Invoice</a>
                    </div>
                    <!--  -->
                    
                    <a class="settings text-black-50 border-bottom mn_4" data_target="4"><i class="fas fa-plug text-danger"></i>&nbsp;Connect API</a>
                    @endif

                    <a class="settings text-info border-bottom" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">Log Out&nbsp;</a>

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
                <div class="card-body bg-white text-black-50 border-bottom"><h5 class="mb-0"><b>{{ $lang::get('custom.api') }}</b></h5></div>

                <div id="connect_api_gui" class="card-body">
                    <span class="wallet mb-2"><!--  --></span>
                    @if($membership == 'free' && $trial == 0)
                        <a class="settings text-bold alert-warning" data_target="2">{!! $lang::get('custom.trial') !!} {{ $lang::get('custom.here') }}</a></div>
                    @else
                        <div class="msg"><!--  --></div>
                        @if($user->watcherviews_id > 0)
                             <div class="alert alert-info"> Silahkan tarik coin anda dari watcherviews <b><a href="{{ url('wallet') }}">disini</a></b></div>
                            <div class="alert alert-info">{{ Lang::get('auth.api') }} : <u><a id="logout_watcherviews">Disconnect API</a></u></div>
                        @else
                            @include('home.connect_api')
                        @endif
                    @endif
                </div>
            </div>
           
            <!-- end col -->
        </div> 
    </div>
    <!-- end justify -->
</div>


<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    var segment = "{{ $conf }}";

    $(document).ready(function(){
        data_tabs();
        load_page();
        save_profile();
        connect_api();
        logout_watcherviews();
    });

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
                        $(".bank_name").html(result.bank_name);
                        $(".bank_no").html(result.bank_no);
                        $(".phone").html(result.phone);
                        $(".phone").html(result.code_country); //exceptional
                        $(".oldpass").html(result.oldpass);
                        $(".confpass").html(result.confpass);
                        $(".newpass").html(result.newpass);
                    }
                    else
                    {
                        $("#phone_number").html(result.phone);
                        $(".msg").html('<div class="alert alert-success">'+result.msg+'</div>');
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
                        $("#connect_api_gui").html('<div class="alert alert-info">{{ Lang::get("auth.api") }} : <b><a id="logout_watcherviews">logout</a>')
                        $("input").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ $lang::get("auth.credential") }}</div>');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ $lang::get("custom.failed") }}</div>');
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
               $(".error").hide();
            },
            success : function(result)
            {
                if(result.err == 0)
                {
                    location.href="{{ url('account') }}/wallet";
                }
                else
                {
                    $(".wallet").html('<div class="alert alert-danger">'+result.err+'</div>');
                }
            },
            complete : function()
            {
               $('#loader').hide();
               $('.div-loading').removeClass('background-load');
            },
            error: function(){
               $('#loader').hide();
               $('.div-loading').removeClass('background-load');
            }
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
    /*var diskon = parseInt($(this).attr('data-discount'));
        if (diskon == 0 ) {
            $("#div-discount").hide();
        }
    $('#mod-discount').html('Rp. ' + diskon.toLocaleString());*/
    $('#mod-date').html($(this).attr('data-date'));

    var keterangan = '-';
   // console.log($(this).attr('data-keterangan'));
    if($(this).attr('data-keterangan')!='' || $(this).attr('data-keterangan')!=null){
      keterangan = $(this).attr('data-keterangan');
    }

    $('#mod-keterangan').html(keterangan);
  });

  // $( "body" ).on( "click", "#btn-confirm-ok", function() 
  // {
    // confirm_payment();
  // });

  $( "body" ).on( "click", ".popup-newWindow", function()
  {
    event.preventDefault();
    window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
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