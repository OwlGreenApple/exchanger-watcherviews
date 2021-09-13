@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Konfimasi Pembelian
                </div>

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
                      </div>
                      <div class="form-group">
                        <label for="ct">Keterangan</label>
                        <textarea name="note" class="form-control"></textarea>
                      </div>
                      <button id="submit" type="button" class="btn btn-success">Kirim</button>
                    </form>
                </div>

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
                    /*else if(result.err == 1)
                    {
                        $(".error").show();
                        
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ Lang::get("custom.failed") }}');
                    }*/
                    else
                    {
                        $(".error").show();
                        $("#err_message").html('{{ Lang::get("custom.failed") }}');
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
