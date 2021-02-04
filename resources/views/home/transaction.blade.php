@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Transaksi Koin</b></h2>  
    
      <hr>
    </div>
  </div>

  <div class="col-lg-10 justify-content-center mt-3 mx-auto bg-white py-2">
    <h5 class="text-center"><b>Daftar Transaksi</b></h5>
    
    <div class="table-responsive">
      <table id="transaction_list" class="table table-striped table-bordered">
        <thead align="center">
          <th width="10%">No</th>
          <th width="25%">Debit</th>
          <th width="25%">Credit</th>
          <th width="20%">Sumber</th>
          <th width="20%">Tanggal</th>
         <!--  <th>Status</th> -->
        </thead>
        <tbody>
          @if($transaction->count() > 0)
            @php $no = 1; @endphp
            @foreach($transaction as $row)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ str_replace(",",".",number_format($row->debit)) }}</td>
                <td>{{ str_replace(",",".",number_format($row->kredit)) }}</td>
                <td>{{ str_replace("-"," ",$row->source) }}</td>
                <td>{{ $row->created_at }}</td>
              </tr>
              @php $no++; @endphp
            @endforeach
          @endif
        </tbody>
      </table>
  </div>

  </div>
<!--  -->
</div>

<script type="text/javascript">

  $(document).ready(function()
  {
    let table = $("#transaction_list").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : [],
    });
  });

</script>

@endsection
