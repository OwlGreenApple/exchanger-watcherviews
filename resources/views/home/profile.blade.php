@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">Profile</div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="profile">
                    
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nama') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" autocomplete="name" autofocus>
                                <span class="error name"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('No HP Anda') }}</label>

                            <div class="col-md-6">
                                <div id="phone_number" class="form-control alert-success">{{ $user->phone_number }}</div>
                                <!--  -->
                                <div class="col-md-12 row mt-2">
                                  <input type="text" id="phone" name="phone" class="form-control"/>
                                    <span class="error phone"></span>

                                    <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
                                   <input name="data_country" type="hidden" /> 
                                </div>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.bank_name') }}</label>

                            <div class="col-md-6">
                                <input type="text" value="{{ $user->bank_name }}" class="form-control" name="bank_name" />
                                <span class="error bank_name"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.bank_no') }}</label>

                            <div class="col-md-6">
                                <input type="text" value="{{ $user->bank_no }}" class="form-control" name="bank_no" />
                                <span class="error bank_no"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.ovo') }}</label>

                            <div class="col-md-6">
                                <input type="file" class="form-control" name="ovo" />
                                <span class="error ovo"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.dana') }}</label>

                            <div class="col-md-6">
                                <input type="file" class="form-control" name="dana" />
                                <span class="error dana"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.gopay') }}</label>

                            <div class="col-md-6">
                                <input type="file" class="form-control" name="gopay" />
                                <span class="error gopay"><!--  --></span>
                            </div>
                        </div>

                        <hr/>

                        <div align="center" class="mb-3"><b>{{ $lang::get('auth.notes') }}</b></div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right"> Password Lama</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="oldpass">
                                <span class="error oldpass"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="newpass">
                                <span class="error newpass"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Konfirmasi Password') }}</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="confpass">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Ubah') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/custom.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        save_profile();
    });

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
                        $("#msg").html('<div class="alert alert-success">'+result.msg+'</div>');
                    }
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

</script>
@endsection
