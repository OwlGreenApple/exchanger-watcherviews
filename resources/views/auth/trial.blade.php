@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">{{ $lang::get('custom.end') }}</div>

                <div class="card-body">
                    <div class="alert alert-info">{!! $lang::get('custom.trial') !!} <a href="{{ url('checkout') }}">{{ $lang::get('custom.here') }}</a></div>
                </div>
                    
            </div>
        </div>
    </div>
</div>
@endsection
