@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-danger text-white">{{ $lang::get('custom.wallet') }}
                    <div class="float-right">{{ $lang::get('custom.coin') }} : <b id="coin">{{ $pc->pricing_format(Auth::user()->coin) }}</b>&nbsp;coin</div>
                    <span class="clearfix"></span>
                </div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="wallet_coin">
                    
                        @if(auth()->user()->watcherviews_id == 0)
                          <div class="alert alert-info">Silahkan hubungkan akun watcherviews anda di link ini <a href="{{ url('account') }}/wallet">connect wallet</a></div>
                        @else

                        <span class="error wallet"><!--  --></span>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.wt') }}</label>

                            <div class="col-md-6">
                               <label for="name" class="col-form-label text-md-right"><b id="total_coin">{!! $coin !!}</b>&nbsp;coin</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right"><!--  --></label>

                            <div class="col-md-6">
                               <div class="form-check">
                                  <input class="form-check-input" type="radio" name="wallet_option" id="flexRadioDefault1" value="1" checked>
                                  <label class="form-check-label" for="flexRadioDefault1">
                                    {{ $lang::get('transaction.wd') }}&nbsp;(min : <b>100.000</b>)
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="wallet_option" value="2" id="flexRadioDefault2">
                                  <label class="form-check-label" for="flexRadioDefault2">
                                    {{ $lang::get('transaction.send') }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.total.coin') }}</label>

                            <div class="col-md-6">
                               <input class="form-control" type="number" name="coin_ammount" />
                               <span class="error coin_ammount"><!--  --></span>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-danger">
                                    {{ $lang::get('transaction.wallet') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            <!--  -->
            @if(auth()->user()->watcherviews_id > 0)
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered w-100" style="font-size : 0.65rem" id="data_transaction">
                      <thead>
                        <th class="menu-nomobile">
                         No
                        </th>
                        <th class="menu-nomobile">
                         {{$lang::get('transaction.no')}}
                        </th>
                        <th class="menu-nomobile">
                          {{$lang::get('transaction.type')}}
                        </th>
                        <th class="menu-nomobile">
                          {{$lang::get('transaction.amount')}}
                        </th>
                        <th class="menu-nomobile">
                          {{$lang::get('transaction.created')}}
                        </th>
                        <th class="header" action="status">
                          {{$lang::get('transaction.status')}}
                        </th>
                      </thead>
                      <tbody>
                        @if($data->count() > 0)
                          @php $no = 1; @endphp
                          @foreach($data as $row)
                             <tr>
                               <td>{{ $no++ }}</td>
                               <td>{{ $row->no }}</td>
                               <td class="text-center">
                                  @if($row->type == 1)
                                    {{ $lang::get('transaction.buy') }}
                                  @elseif($row->type == 2)
                                    {{ $lang::get('transaction.sell') }}
                                  @else
                                    {{ $lang::get('transaction.withdraw') }}
                                  @endif
                                </td>
                               <td class="text-right">{{ str_replace(",",".",number_format($row->amount)) }}</td>
                               <td>{{ $row->created_at }}</td>
                               <td>
                                 @if($row->status == 0 && $row->type == 1)
                                    {{ $lang::get('transaction.buy.status') }}
                                 @elseif($row->status == 1 && $row->type == 1)
                                    {{ $lang::get('transaction.buy.done') }}
                                 @elseif($row->status == 0 && $row->type == 2)
                                    {{ $lang::get('transaction.sell.status') }}
                                 @elseif($row->status == 1 && $row->type == 2)
                                    {{ $lang::get('transaction.sell.progress') }}
                                 @elseif($row->status == 2 && $row->type == 2)
                                    {{ $lang::get('transaction.sell.done') }}
                                 @else
                                    -
                                 @endif
                               </td>
                             </tr>
                          @endforeach
                        @endif
                      </tbody>
                    </table>
                </div>
            </div>
            @endif
            <!--  -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        transaction_coin();
    });

    function transaction_coin()
    {
        $("#wallet_coin").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('wallet-transaction') }}",
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
                        $(".alert-success").show();
                        cur_coin = parseInt(result.wallet_coin);
                        $("#coin").html(formatNumber(cur_coin));
                        $("#total_coin").html(formatNumber(parseInt(result.total_coin)));
                        $("#msg").html('<div class="alert alert-success text-center">{{ $lang::get("custom.success_coin") }}</div>');
                        $("input[name='coin_ammount']").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ $lang::get("custom.failed") }}--</div>');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ $lang::get("custom.failed") }}</div>');
                    }
                    else if(result.err == 'trial')
                    {
                        $(".error").show();
                        $(".wallet").html('<div class="alert alert-danger">{{ Lang::get("custom.trial.wallet") }} <a href="{{ url("checkout") }}">{{ Lang::get("custom.here") }}</a></div>');
                    }
                    else if(result.err == 'validation')
                    {
                        $(".error").show();
                        $(".coin_ammount").html(result.coin_ammount);
                    }
                    else if(result.err == 'coin')
                    {
                        $(".error").show();
                        $(".coin_ammount").html(result.coin);
                    }
                    /*else if(result.pkg !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.pkg);
                    }
                    else if(result.max !== undefined)
                    {
                        $(".error").show();
                        $(".wallet").html(result.max);
                    }*/
                    else
                    {
                        $(".error").show();
                        $(".wt_email").html(result.wt_email);
                        $(".wt_pass").html(result.wt_pass);
                    }
                },
                complete :function()
                {
                  $(".alert-success").delay(3000).fadeOut(2000);
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
