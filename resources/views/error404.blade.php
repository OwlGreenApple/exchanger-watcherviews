@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">


<div class="container konten">
  <div class="offset-sm-2 col-sm-8">
    <div class="card h-80 card-payment" style="margin-bottom: 50px">
      <div class="card-body">
          <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
            <i class="fas fa-check-circle"></i>
          </span>
          <h1>Maaf, halaman yang anda cari tidak ada</h1>
          <br/>
         
          <div class="form-group offset-md-2 col-md-8 col-12">
            <a href="{{url('home')}}" class="free-underline">
              <input type="button" class=" btn btn-primary bsub btn-block" value="{{ Lang::get('order.back') }}" style="margin-top:-10px;" />
            </a>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection
