@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-danger text-white">
                   Summary
                </div>

                <div class="card-body">
                    <div class="err_message"><!-- error messages --></div>
                    <h5>Terima kasih atas pembelian koin anda, silahkan lakukan pembayaran</h5>
                    <div>No Invoice : <b>{{ $row['no'] }}</b></div>
                    <div>Total : <b>{{ $row['total'] }}</b></div>
                    <div>Methode Pembayaran :</b></div>
                    <div class="mb-2">
                        <select name="payment" class="form-control">
                            @if($user->bank_name !== null && $user->bank_no !== null)
                                <option value="bank">Transfer Bank</option>
                            @endif
                            @if($user->ovo !== null)
                                <option value="ovo">OVO</option>
                            @endif
                            @if($user->gopay !== null)
                                <option value="gopay">GoPay</option>
                            @endif
                            @if($user->dana !== null)
                                <option value="dana">Dana</option>
                            @endif
                        </select>
                    </div>

                    <div id="bank">
                        <ul>
                            <li>No Rekening : <b>{{ $user->bank_no }}</b></li>
                            <li>Bank : {{ $user->bank_name }}&nbsp;<b>{{ $user->name }}</b></li>
                        </ul>
                    </div>

                    <div id="electronic">
                         <h5>Silahkan scan qr code di bawah ini sesuai payment method anda <b><span id="pmt"></span></b></h5>
                         <img id='barcode' src="https://api.qrserver.com/v1/create-qr-code/?data=HelloWorld&amp;size=200x200" alt="" title="HELLO" width="200" height="200" />
                    </div>

                    <div class="mt-2">Lakukan konfirmasi apabila sudah melakukan pembayaran disini : <a id="conf" data-id="{{ $row['id'] }}" class="btn btn-info">Konfirmasi</a></div>
                </div>


            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        payment_method()
        buyer_deal();
    });

    function buyer_deal()
    {
        $("#conf").click(function(){
            var id = $(this).attr('data-id');

            $.ajax({
                type : 'GET',
                url : "{{ url('buy-deal') }}",
                dataType : 'json',
                data : {'id':id},
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
                        location.href="{{ url('buyer-confirm') }}/{{ $row['no'] }}";
                    }
                    else
                    {
                        $('#loader').hide();
                        $('.div-loading').removeClass('background-load');
                        $(".err_message").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
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

    function payment_method()
    {
        var first_option = $("select[name='payment'] option").eq(0).val();
        payment_content(first_option);

        $("select[name='payment']").change(function(){
            var value = $(this).val();
            payment_content(value);
        });
    }

    function payment_content(value)
    {
        $("#pmt").html(value);
        if(value == 'bank')
        {
            $("#bank").show();
            $("#electronic").hide();
        }
        else
        {
            $("#bank").hide();
            $("#electronic").show();
        }
    }

</script>

@endsection
