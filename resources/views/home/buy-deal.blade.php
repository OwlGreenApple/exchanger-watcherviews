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
                    <h5>Terima kasih atas pembelian koin anda, silahkan lakukan pembayaran</h5>
                    <div>No Invoice : <b>B-210903-002</b></div>
                    <div>Total : <b>Rp 10.000</b></div>
                    <div>Methode Pembayaran :</b></div>
                    <div class="mb-2">
                        <select name="payment" class="form-control">
                            <option value="bank">Transfer Bank</option>
                            <option value="ovo">OVO</option>
                            <option value="gopay">GoPay</option>
                            <option value="dana">Dana</option>
                        </select>
                    </div>

                    <div id="bank">
                        <ul>
                            <li>No Rekening : <b>1010101010</b></li>
                            <li>Bank : BCA&nbsp;<b>Seller</b></li>
                        </ul>
                    </div>

                    <div id="electronic">
                         <h5>Silahkan scan qr code di bawah ini sesuai payment method anda <b><span id="pmt"></span></b></h5>
                         <img id='barcode' src="https://api.qrserver.com/v1/create-qr-code/?data=HelloWorld&amp;size=200x200" alt="" title="HELLO" width="200" height="200" />
                    </div>

                    <div class="mt-2">Lakukan konfirmasi apabila sudah melakukan pembayaran disini : <a href="{{ url('buy') }}">Konfirmasi</a></div>
                </div>


            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        payment_method()
    });

    function payment_method()
    {
        payment_content('bank');
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
