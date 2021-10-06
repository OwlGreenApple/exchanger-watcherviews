@extends('layouts.app')

@section('content')

            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                  <i class="mdi mdi-home"></i>
                </span> Dashboard </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  @if(Auth::user()->watcherviews_id == 0)
                  <li class="breadcrumb-item " aria-current="page">
                    <span></span><a class="text-danger" href="{{ url('account') }}/wallet"><i class="fas fa-plug"></i>&nbsp;Harap Connect API</a> 
                  </li>
                  @else
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span><i class="fas fa-plug"></i>&nbsp;API Connected
                  </li>
                  @endif
                </ul>
              </nav>
            </div>

            <div class="row">
              <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                  <div class="card-body">
                    <a href="{{ url('buy') }}" class="link-custom">
                      <img src="{{url('assets/template/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3 text-white">Total Penjualan <i class="mdi mdi-chart-line mdi-24px float-right"></i>
                      </h4>
                      <h2 class="mb-5 text-white">{{ Lang::get('custom.currency') }} {{ $pc->pricing_format($total_penjualan) }}</h2>
                      <h6 class="card-text text-white">{{ $sell_status }}</h6>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                  <div class="card-body">
                    <a href="{{ url('buy') }}" class="link-custom">
                      <img src="{{url('assets/template/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3 text-white">Total Pembelian <i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
                      </h4>
                      <h2 class="mb-5 text-white">{{ Lang::get('custom.currency') }} {{ $pc->pricing_format($total_pembelian) }}</h2>
                      <h6 class="card-text text-white">{{ $buy_status }}</h6>
                    </a>
                  </div>
                </div>
              </div>

              <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                  <div class="card-body">
                    <a href="{{ url('wallet') }}" class="link-custom">
                      <img src="{{url('assets/template/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3 text-white">My Coins <i class="mdi mdi-diamond mdi-24px float-right"></i>
                      </h4>
                      <h2 class="mb-5 text-white">{{ $pc->pricing_format(Auth::user()->coin) }}</h2>
                      <h6 class="card-text text-white">{{ $wallet_status }}</h6>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <!-- buy page -->
            @include('buyer.buy-form')
            <!--  --> 

            <!-- SELLING GRAPHIC -->           
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="clearfix">
                      <h4 class="card-title float-left">Statistik Penjualan</h4>
                       <!-- chart -->
                      <!-- <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-right"></div> -->
                    </div>
                    <!-- <canvas id="visit-sale-chart" class="mt-4"></canvas> -->
                    <div id="user-charts" class="wd-100" style="height: 300px;"></div>
                  </div>
                </div>
              </div>
              <!--  -->
            </div>     

            <!-- PURCHASING GRAPHIC -->
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="clearfix">
                      <h4 class="card-title float-left">Statistik Pembelian</h4>
                    </div>
                    <div id="buyer-charts" class="wd-100" style="height: 300px;"></div>
                  </div>
                </div>
              </div>
              <!--  -->
            </div>      

            <!-- WALLET GRAPHIC -->
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="clearfix">
                      <h4 class="card-title float-left">Statistik Transaksi Wallet</h4>
                    </div>
                    <div id="wallet-charts" class="wd-100" style="height: 300px;"></div>
                  </div>
                </div>
              </div>
              <!--  -->
            </div>      

<!-- Modal Confirmation -->
<div class="modal fade" id="confirm_buy" role="dialog">
  <div class="modal-dialog text-center">
    
    <!-- Modal content-->
    <div class="modal-content col-lg-8 col-md-9 col-sm-12 col-12 mx-auto">
      <div class="modal-header" style="display : block">
        <h5 class="mb-0">{{Lang::get('transaction.conf')}}</h5>
        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
      </div>
      <div class="modal-body">
        <div>Penjual : Test</div>
        <div><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <div>Jumlah Coin : 100.000</div>
        <div>Total : <b>Rp 10.000</b></div>
      </div>
      <!--  -->
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-promote" data-dismiss="modal">
          {{Lang::get('order.yes')}}
        </button>
        <button class="btn" data-dismiss="modal">
          {{Lang::get('order.cancel')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">
  var url_global = "{{ url('buy-list') }}";
  $(document).ready(function()
  {
      search_seller();
      pagination();
      request_buy();
  });

  function request_buy()
  {
      $("body").on("click",".request_buy",function()
      {
          var id = $(this).attr('data-id');

          $.ajax({
              type : 'GET',
              url : "{{ url('buy-request') }}",
              dataType : 'html',
              data : {'id' : id},
              beforeSend: function()
              {
                 $('#loader').show();
                 $('.div-loading').addClass('background-load');
                 $(".error").hide();
              },
              success : function(result)
              {
                  display_buy_list(null,null,null);
              },
              complete : function()
              {
                  $('#loader').hide();
                  $('.div-loading').removeClass('background-load');
              },
              error : function()
              {
                  $('#loader').hide();
                  $('.div-loading').removeClass('background-load');
              }
          });
      });
  }

  window.onload = function () 
  {
    /** TOTAL CONTACTS ADDING PER DAY **/
    var contacts = [];
    $.each(<?php echo $data_penjualan; ?>, function( i, item ) {
        contacts.push({'x': new Date(item.created_at), 'y': item.total});
    });

    // contacts.push({x : new Date('2019-12-04'), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle" });

    var chart = new CanvasJS.Chart("user-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Rentang Waktu",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total (Rp)",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints : contacts,
        color : "#2cb09a"
      }]
    });
    chart.render();

    // CHART BUYER

    var buyer = [];
    $.each(<?php echo $data_pembelian; ?>, function( i, item ) {
        buyer.push({'x': new Date(item.created_at), 'y': item.total});
    });

    var buyer_chart = new CanvasJS.Chart("buyer-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Rentang Waktu",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total (Rp)",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints : buyer,
        color : "#0881ff"
      }]
    });
    buyer_chart.render();

    // CHART WALLET

    var wallet = [];
    $.each(<?php echo $wallet_data; ?>, function( i, item ) {
        wallet.push({'x': new Date(item.created_at), 'y': item.coin});
    });

    var wallet_chart = new CanvasJS.Chart("wallet-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Rentang Waktu",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total (coin)",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints : wallet,
        color : "#e77e11"
      }]
    });
    wallet_chart.render();

  /**/  
  }

</script>
<script src="{{ asset('assets/js/buyer.js') }}" type="text/javascript"></script>
@endsection
