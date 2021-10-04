@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 style="font-size : 0.95rem" class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span>Pesan untuk notifikasi order WA</h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">

            <div id="msg"><!-- message --></div>

            <div class="card-body">
                <form id="message">
                
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Notifikasi setelah order') }}</label>

                        <div class="col-md-6">
                            <textarea class="form-control" name="notif">{{ $notif->notif_order }}</textarea>
                            <span class="error notif"><!--  --></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right"> Notifikasi 6 jam setelah order</label>

                        <div class="col-md-6">
                            <textarea class="form-control" name="notif_order">{{ $notif->notif_after }}</textarea>
                            <span class="error notif_order"><!--  --></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right"> Daftar variabel yg bisa dipakai</label>

                        <div class="col-md-6">
                            <ul>
                                <li>[NO-ORDER]</li>
                                <li>[PACKAGE]</li>
                                <li>[PRICE]</li>
                                <li>[TOTAL]</li>
                                <!-- <li>[LINK]</li> -->
                            </ul>
                        </div>
                    </div>

                    <div class="form-group row">
                       <!--  <label class="col-md-4 col-form-label text-md-right">Admin id activrespon</label> -->

                        <div class="col-md-6">
                            <input type="hidden" readonly="readonly" class="form-control" name="admin_id" value="{{ $notif->admin_id }}" />
                            <span class="error admin_id"><!--  --></span>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Simpan') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!--  -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        save_message();
    });

    function save_message()
    {
        $("#message").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('save-message') }}",
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
                        $(".notif").html(result.notif);
                        $(".notif_order").html(result.notif_order);
                        $(".admin_id").html(result.admin_id);
                    }
                    else
                    {
                        $(".error").hide();
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
