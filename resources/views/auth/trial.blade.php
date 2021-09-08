@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center mt-3">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning text-center">{!! $lang::get('custom.trial') !!} <a href="{{ url('checkout') }}">{{ $lang::get('custom.here') }}</a></div> 
             </div> 
        </div> 
    </div>
</div>         
@endsection