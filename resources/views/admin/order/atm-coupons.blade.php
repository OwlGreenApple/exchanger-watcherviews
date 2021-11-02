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
    <div class="col-md-12 col-lg-12 table-responsive">
      <table class="table" id="coupon_table">
        <thead>
          <th>No</th>
          <th>Nama</th>
          <th>Kode kupon</th>
          <th>Voucher</th>
          <th>Tanggal Dibuat</th>
          <th>Tanggal Diambil</th>
          <th>Status</th>
        </thead>
        <tbody>
          @if($data->count() > 0)
            @php $no = 1; @endphp

            @foreach($data as $row)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->code }}</td>
                <td>@if($row->type == 1) 100.000 @else 200.000 @endif</td>
                <td>{{ $row->created_at }}</td>
                <td>{{ $row->date_used }}</td>
                <td>@if($row->is_used == 1) <span class="text-success">Terpakai</span> @else Belum Terpakai @endif</td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
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

        <form id="import_coupon">
          <input type="file" class="form-control" name="upload_coupon" />
          <button type="submit" class="btn btn-success">Import</button>
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
    $("#modal_import").modal();

    $("#import_coupon").submit(function(e){
      e.preventDefault();

      var form = $(this)[0];
      var data = new FormData(form);
      import_coupon(data);
    });
  }

  function import_coupon(data)
  {
    $.ajax({
      type : 'GET',
      url : "{{ url('import-coupon') }}",
      cache : false,
      contentType: false,
      processData : false,
      dataType : 'json',
      data : data,
      beforeSend: function()
      {
         $('#loader').show();
         $('.div-loading').addClass('background-load');
      },
      success : function(result)
      {
          
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
    $("#coupon_table").DataTable({"responsive":true});
  }
</script>
@endsection