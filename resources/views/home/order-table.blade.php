
<table class="table responsive" id="order-table">
    <thead align="center">
      <th>No Order</th>
      <th>Package</th>
      <th>Price</th>
      <th>Total</th>
      <th>Date</th>
      <th>Upload Image</th>
      <th>Notes</th>
      <th style="width:145px">Status</th>
    </thead>

    <tbody>
       @foreach($orders as $order)
        <tr>
          <td>{{$order->no_order}}</td>
          <td>{{$order->package}}</td>
          <td>{{$order->price}}</td>
          <td>{{$order->total}}</td>
          <td>{{$order->created_at}}</td>
          <td>
            @php
              if($order->buktibayar !== null || $order->buktibayar !== "") 
              {
                $buktibayar = 1; 
              }
              else
              {
                $buktibayar = 0;
              }
            @endphp

            @if($buktibayar == 1)
              {{ $buktibayar }}
            @else
              -
            @endif
          </td>
          <td>-</td>
          <td>
            @if($order->status==0)
              <button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#confirm-payment" data-id="{{$order->id}}" data-no-order="{{$order->no_order}}" data-package="{{$order->package}}" data-total="{{$order->grand_total}}" data-discount="{{$order->discount}}" data-date="{{$order->created_at}}" data-keterangan="" style="font-size: 13px; padding: 5px 8px;">
                Confirm Payment
              </button>
            @elseif($order->status==1)
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
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ]
    });
  }
</script>