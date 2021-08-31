@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">Kurs harga coin hari ini</div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="message">

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Kurs Sekarang</label>

                            <div class="col-md-6">
                                <div class="form-control" id="kurs">{{ Lang::get('custom.currency') }}&nbsp;0.1 / coin</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Kurs baru</label>

                            <div class="col-md-6">
                                <input type="number" class="form-control" name="kurs"  />
                                <span class="error kurs"><!--  --></span>
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
