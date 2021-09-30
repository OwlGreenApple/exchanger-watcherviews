@extends('layouts.app')
<link href="{{ asset('assets/css/comments.css') }}" rel="stylesheet" />

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span>Penilaian</h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
               Nama Penjual : <b>{{ $seller->name }}</b>
            </div>

            <div id="comments" class="card-body">
                <!-- display table comments -->
            </div>
            
            @if($tr->status == 3 && (auth()->user()->id == $tr->buyer_id))
            <div class="card-body">
                <span id="err_message"><!--  --></span>
                <div class="form-group">
                     <div class="bg-light p-2">
                        <div class="px-1 py-1">Buat Komentar :</div>
                        <div class="px-1 py-1"><b>{{ $tr->no }}</b></div>
                        <div class="px-1 py-2 text-black-50">
                            <i class="rate fas fa-star"></i>
                            <i class="rate fas fa-star"></i>
                            <i class="rate fas fa-star"></i>
                            <i class="rate fas fa-star"></i>
                            <i class="rate fas fa-star"></i>
                        </div>
                        <div class="d-flex flex-row align-items-start">
                            <textarea name="comments" class="form-control ml-1 shadow-none textarea"></textarea>
                        </div>
                         <div class="error comments px-1"><!-- error --></div>
                        <div class="mt-2 text-right">
                            <button id="save_comments" class="btn btn-primary btn-sm shadow-none" type="button">Post</button><button class="btn btn-outline-primary btn-sm ml-1 shadow-none" type="button">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--  -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        display_comments();
        save_comments();
        rate();
    });

    function rate()
    {
        $(".rate").click(function(){
            var pos = $(".rate").index(this);
            var check = $(this).hasClass( "checked" );

            if(check == true)
            {
                 $(this).removeClass('checked');
                 star_min(pos)
            }
            else
            {
                 $(this).addClass('checked');
                 star_plus(pos)
            }
        })
    }

    function star_plus(pos)
    {
        if(pos == 0)
        {
            for(x=1;x<5;x++)
            {
                $(".rate").eq(x).removeClass('checked');
            }
        }
        else
        {
            for(x=0;x<pos;x++)
            {
                $(".rate").eq(x).addClass('checked');
            }
        }
    }

    function star_min(pos)
    {
        for(x=4;x>pos;x--)
        {
            $(".rate").eq(x).removeClass('checked');
        }
    }

    function display_comments()
    {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type : 'POST',
            url : "{{ url('display-comments') }}",
            dataType : 'html',
            data : {'seller_id' : "{{ $tr->seller_id }}"},
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
               $(".error").hide();
            },
            success : function(result)
            {
                $("#comments").html(result);
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

    function save_comments()
    {
        $("#save_comments").click(function(){
            var comments = $('textarea[name="comments"]').val();
            var rate = $(".checked").length;
            var data = {
                'comments' : comments,
                'rate' : rate,
                'seller_id' : "{{ $tr->seller_id }}",
                'no_trans' : "{{ $tr->no }}",
            };

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('save-comments') }}",
                dataType : 'json',
                data : data,
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
                       display_comments();
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
