@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">

<div class="page-header">
  <div class="container konten">
    <div class="offset-sm-2 col-sm-8">
      <div class="card h-80 card-payment" style="margin-bottom: 50px">
        <div class="card-body">
            <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
              <i class="fas fa-plug"></i>
            </span>
            <h1>Selamat Datang</h1>
            <br/>
            <p>Silahkan connect kan akun watcherviews anda disini : <a href="{{url('account')}}/wallet">Connect API</a></p>
            <p>Atau silahkan download watcherviews di playstore</p>
      
          </div>
        </div>
      </div>
  </div>
</div>

@endsection
