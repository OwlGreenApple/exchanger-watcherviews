@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-danger text-white">{{ $lang::get('custom.wallet') }}
                    <div class="float-right">{{ $lang::get('custom.coin') }} : <b>{{ $pc->pricing_format(Auth::user()->coin) }}</b></div>
                    <span class="clearfix"></span>
                </div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="profile">
                    
                        <div class="alert alert-info">Silahkan hubungkan akun watcherviews anda di link ini <a href="{{ url('connect_api') }}">connect api</a></div>

                        <span class="error wallet"><!--  --></span>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.wt') }}</label>

                            <div class="col-md-6">
                               <label for="name" class="col-form-label text-md-right"><b>50.000</b></label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right"><!--  --></label>

                            <div class="col-md-6">
                               <div class="form-check">
                                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked>
                                  <label class="form-check-label" for="flexRadioDefault1">
                                    {{ $lang::get('transaction.wd') }}
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                  <label class="form-check-label" for="flexRadioDefault2">
                                    {{ $lang::get('transaction.send') }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ $lang::get('transaction.total.coin') }}</label>

                            <div class="col-md-6">
                               <input class="form-control" type="number" name="wd_coin" />
                               <span class="error wd_coin"><!--  --></span>
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
            </div>
            <!--  -->
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
            <!--  -->
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
                        $("#msg").html('<div class="alert alert-success">{{ $lang::get("custom.success_coin") }}</div>');
                        $("input").val('');
                    }
                    else if(result.err == 1)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ $lang::get("auth.credential") }}');
                    }
                    else if(result.err == 2)
                    {
                        $(".error").show();
                        $(".wallet").html('{{ $lang::get("custom.failed") }}');
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
