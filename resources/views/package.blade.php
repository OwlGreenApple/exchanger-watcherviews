@extends('layouts.app')

@section('content')
    <div class="container mb-5 mt-5">
    <div class="pricing card-deck flex-column flex-md-row mb-3">
    
        @if(count( $pc->get_price() ) > 0)
            @foreach($pc->get_price() as $index=>$row)
                @if($index > 0)
                    @if($index == 2)
                    <div class="card card-pricing popular shadow text-center px-3 mb-4">
                        <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm text-capitalize">{{ $pc->get_price()[$index]['package'] }}</span>
                        <div class="bg-transparent card-header pt-4 border-0">
                            <h3 class="h3 font-weight-normal text-primary text-center mb-0" data-pricing-value="30">{{ Lang::get('custom.currency') }}&nbsp;<span class="price">{{ $pc->pricing_format($pc->get_price()[$index]['price']) }}</span><hr><div class="h6 text-muted ml-2"><span class="text-capitalize">{{ Lang::get('custom.package_terms') }}</span></div></h3>
                        </div>
                        <!--  -->
                        <div class="card-body pt-0">
                            <ul class="list-unstyled mb-4">
                                <li>{{ Lang::get('order.month_sell') }} : {{ Lang::get('custom.currency') }}&nbsp;<b>{{ $pc->pricing_format($pc->get_price()[$index]['max_sell']) }}</b></li>
                                <li>{{ Lang::get('order.coin_fee') }} <b>{{ $pc->get_price()[$index]['fee'] }}</b>%</li>
                                <li>{{ Lang::get('order.selling') }} : <b>{{ $pc->get_price()[$index]['sell'] }}</b> {{ Lang::get('order.max_trans') }}</li>
                            </ul>
                            <a href="{{ url('checkout') }}/{{$index}}" target="_blank" class="btn btn-primary mb-3">{{ Lang::get('order.order') }}</a>
                        </div>
                    </div>
                    @else
                    <div class="card card-pricing shadow text-center px-3 mb-4">
                        <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm text-capitalize">{{ $pc->get_price()[$index]['package'] }}</span>
                        <div class="bg-transparent card-header pt-4 border-0">
                            <h3 class="h3 font-weight-normal text-primary text-center mb-0" data-pricing-value="30">{{ Lang::get('custom.currency') }}&nbsp;<span class="price">{{ $pc->pricing_format($pc->get_price()[$index]['price']) }}</span><hr><div class="h6 text-muted ml-2"><span class="text-capitalize">{{ Lang::get('custom.package_terms') }}</span></div></h3>
                        </div>
                        <!--  -->
                        <div class="card-body pt-0">
                            <ul class="list-unstyled mb-4">
                                <li>{{ Lang::get('order.month_sell') }} : {{ Lang::get('custom.currency') }}&nbsp;<b>{{ $pc->pricing_format($pc->get_price()[$index]['max_sell']) }}</b></li>
                                <li>{{ Lang::get('order.coin_fee') }} <b>{{ $pc->get_price()[$index]['fee'] }}</b>%</li>
                                <li>{{ Lang::get('order.selling') }} : <b>{{ $pc->get_price()[$index]['sell'] }}</b> {{ Lang::get('order.max_trans') }}</li>
                            </ul>
                            <a href="{{ url('checkout') }}/{{$index}}" target="_blank" class="btn btn-primary mb-3">{{ Lang::get('order.order') }}</a>
                        </div>
                    </div>
                    @endif
                @endif
            @endforeach
        @endif
       <!--  -->
    </div>

    @if(session::get('reg') !== null)
        <div class="border rounded bg-white row py-3" align="center">
            <div class="col-lg-4 text-justify pb-2">
                <div>Tidak yakin untuk memilih?<br/>Silahkan coba</div>
                <div><b>Free</b></div>
                <div><b>{{ Lang::get('custom.currency') }} 0</b></div>
            </div>
            <div class="col-lg-4 text-justify">
                <ul class="list-unstyled">
                    <li>{{ Lang::get('order.month_sell') }} : {{ Lang::get('custom.currency') }}&nbsp;<b>{{ $pc->pricing_format($pc->get_price()[0]['max_sell']) }}</b></li>
                    <li>{{ Lang::get('order.coin_fee') }} <b>{{ $pc->get_price()[0]['fee'] }}</b>%</li>
                    <li>{{ Lang::get('order.selling') }} : <b>{{ $pc->get_price()[0]['sell'] }}</b> {{ Lang::get('order.max_trans') }}</li>
                </ul>
            </div>
            <div class="col-lg-4 text-justify"><a class="btn btn-primary" href="{{ url('register-redirect') }}">{{ Lang::get('order.order') }}</a></div>
        </div>
    @endif
</div>
@endsection
