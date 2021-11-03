@extends('layouts.app')

@section('content')
<div class="page-header">
  <h3 style="font-size : 0.95rem" class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span>Daftar ActivTemplate Kupon</h3>
</div>

<div class="mb-5 main-cont">
  <div><button id="import_open" type="button" class="btn btn-success">Import</button></div>
  <div class="row card px-2 py-3">
    <div id="content" class="col-md-12 col-lg-12 table-responsive">
      <!-- table -->
    </div>
  <!-- order user -->
  </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="modal_import" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Import Kupon
        </h5>
      </div>
      <div class="modal-body">
        <form class="form-group" id="import_coupon">
          <input type="file" class="form-control" name="upload_coupon" />
          <button type="submit" class="btn btn-success btn-sm mt-2">Import</button>
        </form>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    display_table();
    open_import();
  });

  function open_import()
  {
    $("#import_open").click(function()
    {
      $("#modal_import").modal();
    });

    $("#import_coupon").submit(function(e){
      e.preventDefault();

      var data = new FormData($(this)[0]);
      import_coupon(data);
    });
  }

  function import_coupon(data)
  {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      type : 'POST',
      url : "{{ url('import-coupon') }}",
      contentType: false,
      processData : false,
      data : data,
      dataType : 'json',
      beforeSend: function()
      {
         $('#loader').show();
         $('.div-loading').addClass('background-load');
      },
      success : function(result)
      {
          if(result.response == 1)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $("#modal_import").modal('hide');
          }
      },
      complete : function()
      {
          display_table();
      },
      error : function()
      {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
      }
  });
  }

  function display_table()
  {
    $.ajax({
        type : 'GET',
        url : "{{ url('display-atm-coupon') }}",
        dataType : 'html',
        beforeSend: function()
        {
           $('#loader').show();
           $('.div-loading').addClass('background-load');
        },
        success : function(result)
        {
           $("#content").html(result);
        },
        complete : function()
        {
           $('#loader').hide();
           $('.div-loading').removeClass('background-load');
           $("#coupon_table").DataTable({"responsive":true});
        },
        error : function()
        {
           $('#loader').hide();
           $('.div-loading').removeClass('background-load');
        }
    });

  }
</script>
@endsection