@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Seller Dispute
                </div>

                <div class="card-body">
                    <form action="/action_page.php">
                          <div class="form-group">
                            <label for="email">No Invoice:</label>
                            <div class="form-control">B-20210902-001</div>
                          </div>
                          <div class="form-group">
                            <label for="fl">Upload Bukti</label>
                            <input type="file" class="form-control" name="bukti" id="fl">
                          </div>
                          <div class="form-group">
                            <label for="ct">Komentar</label>
                            <textarea class="form-control"></textarea>
                          </div>
                          <button type="submit" class="btn btn-success">Kirim</button>
                        </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        withdraw_coin();
    });

    function withdraw_coin()
    {
        $("#profile").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('wallet-top-up') }}",
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
                        var cur_coin = $("#coin").attr('data-coin');
                        cur_coin = parseInt(cur_coin);
                        cur_coin += result.coin;
                        $("#coin").html(formatNumber(cur_coin));
                        $("#msg").html('<div class="alert alert-success">{{ Lang::get("custom.success_coin") }}</div>');
                        $("input").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ Lang::get("auth.credential") }}');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ Lang::get("custom.failed") }}');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                    else if(result.pkg !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.pkg);
                    }
                    else if(result.max !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.max);
                    }
                    else
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
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
