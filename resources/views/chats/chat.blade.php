@extends('layouts.app')
<link href="{{ asset('assets/css/comments.css') }}" rel="stylesheet" />

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Chat invoice : {{ $invoice }}
                </div>

                <div id="chats" class="card-body">
                    <!-- display table comments -->
                    <!-- include('chats.chat-data') -->
                </div>
                
                <div class="card-body">
                    <span id="err_message"><!--  --></span>
                    <div class="form-group">
                         <div class="bg-light p-2">
                            <div class="px-1 py-2">Kirim Pesan</div>
                            
                            <div class="d-flex flex-row align-items-start">
                                <textarea name="comments" class="form-control ml-1 shadow-none textarea"></textarea>
                            </div>
                             <div class="error comments px-1"><!-- error --></div>
                            <div class="mt-2 text-right">
                                <button id="save_comments" class="btn btn-primary btn-sm shadow-none" type="button">Kirim</button><button class="btn btn-outline-primary btn-sm ml-1 shadow-none" type="button">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--  -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function()
    {
        chat();
        chat_interval();
        save_comments();
    });

    function chat_interval()
    {
        setInterval(
            function()
            { 
                chat(); 
            }
        , 5000);
    }

    function chat()
    {
        var results;
        $.ajax({
            // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type : 'GET',
            url : "{{ url('display_chat') }}",
            dataType : 'html',
            data : {'tr_id' : "{{ $tr_id }}"},
            /*beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
            },*/
            success : function(result)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                results = result;
            },
            complete : function()
            {
                $("#chats").html(results);
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function save_comments()
    {
        $("#save_comments").click(function(){
            var comments = $('textarea[name="comments"]').val();
            var data = {
                'comments' : comments,
                'tr_id' : "{{ $tr_id }}"
            };

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('save-chats') }}",
                dataType : 'json',
                data : data,
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                   // $(".error").hide();
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                       chat();
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $("#err_message").html('<div class="alert alert-danger">{{ Lang::get("custom.chat_end") }}</div>');
                    } 
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".comments").html(result.comments);
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

</script>
@endsection
