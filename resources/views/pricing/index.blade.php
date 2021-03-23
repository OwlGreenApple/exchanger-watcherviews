@extends('layouts.app')

@section('content')

<section class="pricing py-5 bg-custom">
  <div class="container">
    <div class="row">
      <!-- Free Tier -->
      <div class="col-lg-4 px-4 wrapper">
        <div class="pricing-header pro">
          <h4 class="card-title text-white text-uppercase text-center">PRO</h4>
        </div>
         <h6 class="card-price text-center">IDR 295.000<span class="period">/Tahun</span></h6>
        <div class="card mb-5 mb-lg-0">
          <div class="card-body">
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Berlaku 1 Tahun</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 1.000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span><b>Disc 10%</b> untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
             <!--  <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Monthly Status Reports</li> -->
            </ul>
            <hr/>
            <a href="{{ url('checkout/1') }}" class="btn btn-block btn-pro text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
      <!-- Plus Tier -->
      <div class="col-lg-4 px-4 wrapper">
        <div class="pricing-header master">
          <h4 class="card-title text-white text-uppercase text-center">MASTER</h4>
        </div>
        <h6 class="card-price text-center">IDR 395.000<span class="period">/Tahun</span></h6>
        <div class="card mb-5 mb-lg-0">
          <div class="card-body">
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Berlaku 1 Tahun</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 2.000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span><b>Disc 18%</b> untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
            </ul>
            <hr>
            <a href="{{ url('checkout/2') }}" class="btn btn-block btn-master text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
      <!-- Pro Tier -->
      <div class="col-lg-4 px-4 wrapper">
        <div class="pricing-header super">
          <h4 class="card-title text-white text-uppercase text-center">SUPER</h4>
        </div>
        <h6 class="card-price text-center">IDR 495.000<span class="period">/Tahun</span></h6>
        <div class="card">
          <div class="card-body">
            <ul class="fa-ul">
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Berlaku 1 Tahun</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Bonus 3.000.000 Koin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span><b>Disc 28%</b> untuk setiap pembelian Coin</li>
              <li><span class="fa-li"><i class="fas fa-check"></i></span>Fitur Drip Views</li>
            </ul>
            <hr>
            <a href="{{ url('checkout/3') }}" class="btn btn-block btn-super text-uppercase">Beli Sekarang</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection