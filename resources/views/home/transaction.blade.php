@extends('layouts.app')

@section('content')

<div class="bg-custom">
  <div class="container">
    <div class="col-md-12 py-4">
      <h2 class="mb-0"><b class="mr-2">Transaksi Koin</b><i class="fas fa-exchange-alt"></i></h2>  
    </div>
  </div>
</div>

<hr>

<div class="container mb-5 main-cont">
  <div class="col-lg-12 justify-content-center mt-3 mx-auto bg-white py-2">
    <h5 class="text-center"><b>Daftar Transaksi</b></h5>
    
    <div class="table-responsive">
      <table id="transaction_list" class="table table-striped table-bordered">
        <thead align="center">
          <th width="10%">No</th>
          <th width="20%">Tanggal</th>
          <th width="25%">Dapat Koin (+)</th>
          <th width="25%">Pakai Koin (-)</th>
          <th width="20%">Sumber</th>
          
         <!--  <th>Status</th> -->
        </thead>
        <tbody>
          @if($transaction->count() > 0)
            @php $no = 1; @endphp
            @foreach($transaction as $row)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $row->created_at }}</td>
                <td>{{ str_replace(",",".",number_format($row->debit)) }}</td>
                <td>{{ str_replace(",",".",number_format($row->kredit)) }}</td>
                <td>{{ str_replace("-"," ",$row->source) }}</td>
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
