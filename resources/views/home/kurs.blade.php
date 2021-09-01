@extends('layouts.app')

@section('content')

<div class="container main-cont">
    <div class="col-md-12 mb-5">
      <h2><b>{{Lang::get('custom.trade')}}</b></h2>  
      <h5></h5>
      <hr>
    </div>

    <div class="col-md-12">
      <!-- chart -->
      <div id="user-charts" class="wd-100" style="height: 300px;"></div>
    </div>
  <!-- end container -->
</div>

<script type="text/javascript">
  window.onload = function () 
  {
    /** TOTAL CONTACTS ADDING PER DAY **/
    var contacts = [];
    /*$.each(php echo json_encode($graph_contacts);, function( i, item ) {
        contacts.push({'x': new Date(i), 'y': item});
    });*/

    var chart = new CanvasJS.Chart("user-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Total registered users in 30 days",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total registered users",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints: [
        {x : new Date('2021-08-04'), y: 1, indexLabel: "highest",markerColor: "green", markerType: "triangle" },
        {x : new Date('2021-09-01'), y: 0.1, indexLabel: "lowes",markerColor: "red", markerType: "triangle" },
        ],
        color : "#2cb06a"
      }]
    });
    chart.render();
    //{x : new Date('2019-12-04'), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle" },
  }
</script>
@endsection