@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span>Summary
  </h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body col-12 col-md-12 card-font-size">
                <div class="err_message"><!-- error messages --></div>
                <h5 class="alert alert-info">Terima kasih atas pembelian koin anda, silahkan lakukan pembayaran</h5>

                <div class="form-group">
                    <label>No Invoice :</label>
                    <div class="form-control form-control-sm border-top-0 border-left-0 border-right-0"><b>{{ $row['no'] }}</b></div>
                </div>

                <div class="form-group">
                    <label>Total :</label>
                    <div class="form-control form-control-sm border-top-0 border-left-0 border-right-0"><b>{{ $row['total'] }}</b></div>
                </div>

                <div class="form-group">
                    <label>Methode Pembayaran :</label>
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
                </div>
                
                <div class="form-group">
                    <div id="bank">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">No Rekening : <b>{{ $user->bank_no }}</b></li>
                            <li class="list-group-item">Bank : {{ $user->bank_name }}</li>
                            <li class="list-group-item">A/N : <b>{{ $user->name }}</b></li>
                        </ul>
                    </div>

                    <div id="electronic">
                         <h5 class="alert alert-secondary">Silahkan scan qr code di bawah ini sesuai payment method anda : <b><span id="pmt"></span></b></h5>

                        <div id="pay_ovo">
                            <img src="{{ $row['ovo'] }}" width="150" height="150" />
                        </div>
                        <div id="pay_dana">
                            <img src="{{ $row['dana'] }}" width="150" height="150" />
                        </div>
                        <div id="pay_gopay">
                            <img src="{{ $row['gopay'] }}" width="150" height="150" />
                        </div>
                    </div>
                </div>

                <div class="mt-4 form-group">
                    <label><u>Lakukan konfirmasi apabila sudah melakukan pembayaran disini :</u></label>

                    <div class="border-top-0 border-left-0 border-right-0"><a id="conf" data-id="{{ $row['id'] }}" class="btn btn-info">Konfirmasi</a></div>
                </div>

            <!-- end card body -->
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
            var payment_method = $("select[name='payment'] option:selected").val();

            $.ajax({
                type : 'GET',
                url : "{{ url('buy-deal') }}",
                dataType : 'json',
                data : {'id':id, 'payment_method' : payment_method},
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
                        location.href="{{ url('buyer-confirm') }}/{{ $row['id'] }}";
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

            if(value == 'ovo')
            {
                $("#pay_ovo").show();
                $("#pay_dana").hide();
                $("#pay_gopay").hide();
            }
            else if(value == 'dana')
            {
                $("#pay_ovo").hide();
                $("#pay_dana").show();
                $("#pay_gopay").hide();
            }
            else
            {
                $("#pay_ovo").hide();
                $("#pay_dana").hide();
                $("#pay_gopay").show();
            }
        }
    }

</script>

@endsection
