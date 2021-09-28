@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span>Konfimasi Pembelian</h3>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                <div id="err_message"><!--  --></div>
                <form id="proof">
                  <div class="form-group">
                    <label for="email">No Invoice:</label>
                    <div class="form-control">{{ $row->no }}</div>
                  </div>
                  <div class="form-group">
                    <label for="fl">Upload Bukti Bayar</label>
                    <input type="file" class="form-control" name="bukti" id="fl">
                    <span class="error bukti"><!--  --></span>
                  </div>
                
                  <button id="submit" type="button" class="btn btn-success">Kirim</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        upload_proof();
    });

    function upload_proof()
    {
        $("#submit").click(function(){
           
            var form = $("#proof")[0];
            var formData = new FormData(form);
            formData.append('id', '{{ $row->id }}'); // added
        
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('buyer-proof') }}",
                cache : false,
                contentType: false,
                processData : false,
                data : formData,
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
                        location.href="{{ url('buy') }}";
                    }
                    else if(result.err == 2)
                    {
                        $("#err_message").html('<div class="alert alert-danger">{{ Lang::get("transaction.proof") }}</div>');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".bukti").html(result.bukti);
                        $(".note").html(result.note);
                    }
                    else
                    {
                        $("#err_message").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
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

    function formatNumber(num) 
    {
        num = parseInt(num);
        if(isNaN(num) == true)
        {
           return '';
        }
        else
        {
           return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }
    }

</script>
@endsection
