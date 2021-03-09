@extends('layouts.app')

@section('content')

<section class="pricing py-5">
  <div class="container">
    <div class="row">
      <!-- Free Tier -->
      <div class="col-lg-4">
        <div class="card mb-5 mb-lg-0">
          <div class="card-body">
            <h5 class="card-title text-muted text-uppercase text-center">PRO</h5>
            <h6 class="card-price text-center">IDR 295.000<span class="period">/Tahun</span></h6>
            <hr>
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 1000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Disc 10% untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
             <!--  <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Monthly Status Reports</li> -->
            </ul>
            <a href="{{ url('checkout/1') }}" class="btn btn-block btn-primary text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
      <!-- Plus Tier -->
      <div class="col-lg-4">
        <div class="card mb-5 mb-lg-0">
          <div class="card-body">
            <h5 class="card-title text-muted text-uppercase text-center">MASTER</h5>
            <h6 class="card-price text-center">IDR 395.000<span class="period">/Tahun</span></h6>
            <hr>
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 2000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Disc 18% untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
            </ul>
            <a href="{{ url('checkout/2') }}" class="btn btn-block btn-primary text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
      <!-- Pro Tier -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title text-muted text-uppercase text-center">SUPER</h5>
            <h6 class="card-price text-center">IDR 495.000<span class="period">/Tahun</span></h6>
            <hr>
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 3000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Disc 28% untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
            </ul>
            <a href="{{ url('checkout/3') }}" class="btn btn-block btn-primary text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection