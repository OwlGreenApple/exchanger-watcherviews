<table class="order-column compact stripe" id="order-table">
    <thead align="center">
      <th>No Order</th>
      <th>Tanggal</th>
      @if($stlabel == 'membership-history')
        <th>Package</th>
      @endif
      @if($stlabel == 'coin-history')
        <th>Koin yang dibeli</th>
      @endif
      <th>Harga</th>
      <th>Total</th>
      <th>Upload Bukti</th>
      <th>Catatan</th>
      <th style="width:145px">Status</th>
    </thead>

    <tbody>
       @foreach($orders as $order)
        <tr>
          <td>{{$order['no_order']}}</td>
          <td>{{$order['created_at']}}</td>
          @if($stlabel == 'membership-history')
            <td>{{$order['package']}}</td>
          @endif
          @if($stlabel == 'coin-history')
            <td>{{ number_format($order['purchased_coins']) }}</td>
          @endif
          <td>{{ number_format($order['price']) }}</td>
          <td>{{ number_format($order['total']) }}</td>
          <td class="text-center">
            @if($order['buktibayar'] !== 0)
              <a class="open_proof" data-href="{!! Storage::disk('s3')->url($order['buktibayar']) !!}">Lihat</a>
            @else
              -
            @endif
          </td>
          <td>{{$order['note']}}</td>
          <td class="text-center">
            @if($order['status']==0)
              <button type="button" class="btn btn-custom btn-confirm" data-toggle="modal" data-target="#confirm-payment" data-id="{{$order['id']}}" data-no-order="{{$order['no_order']}}" data-package="{{$order['package']}}" data-total="{{$order['total']}}" data-date="{{$order['created_at']}}" data-keterangan="" style="font-size: 13px; padding: 5px 8px;">
                Confirm Payment
              </button>
            @elseif($order['status']==1)
              <b style="color: orange">Waiting Admin Confirmation</b>
            @else 
                <b style="color: green">Confirmed</b>
            @endif
          </td>
        </tr>
       @endforeach
    </tbody>
</table>

<script type="text/javascript">
  $(function(){
    tables();
  });

  function tables()
  {
    $("#order-table").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : []
    });
  }
</script>