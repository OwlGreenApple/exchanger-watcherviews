@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 style="font-size : 0.95rem" class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span>Kurs harga coin hari ini</h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">

            <div id="msg"><!-- message --></div>

            <div class="card-body">
                <form id="save-rate">

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Kurs Sekarang</label>

                        <div class="col-md-6">
                            <div class="form-control" id="kurs">{{ Lang::get('custom.currency') }}&nbsp;0.1 / coin</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Kurs baru</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="kurs"  />
                            <span class="error kurs"><!--  --></span>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Simpan') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!--  -->
            <div class="mt-4 px-2">
                  <!-- chart -->
                  <div id="user-charts" class="wd-100" style="height: 300px;"></div>
            </div>
            <!--  -->
        </div>
    </div>
</div>

<script type="text/javascript">

  $(document).ready(function(){
        save_rate();
    });

    function save_rate()
    {
        $("#save-rate").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type : 'POST',
                url : "{{ url('save-rate') }}",
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
                    if(result.status == 'error')
                    {
                        $('#loader').hide();
                        $('.div-loading').removeClass('background-load');
                        $(".error").show();
                        $("#msg").html('<div class="alert alert-danger">'+result.msg+'</div>');
                    }
                    else
                    {
                        $(".error").hide();
                        $("#msg").html('<div class="alert alert-success">'+result.msg+'</div>');
                    }
                },
                complete : function()
                {
                    location.href="{{ url('kurs-admin') }}";
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
    $.each(<?php echo json_encode($data);?>, function( i, item ) {
        contacts.push({'x': new Date(i), 'y': item});
    });

    var chart = new CanvasJS.Chart("user-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Pergerakan kurs",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total Kurs",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints : contacts,
        color : "#2cb06a"
      }]
    });
    chart.render();
    //{x : new Date('2019-12-04'), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle" },
  }
</script>
@endsection
