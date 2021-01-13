@foreach($orders as $order)
  <tr>
    <td>{{$order['no_order']}}</td>
    <td>{{$order['package']}}</td>
    <td>{{ number_format($order['price']) }}</td>
    <td>{{ number_format($order['total']) }}</td>
    <td>{{$order['created_at']}}</td>
    <td class="text-center">
      @if($order['buktibayar'] !== 0)
        <a class="open_proof" data-href="{!! Storage::disk('s3')->url($order['buktibayar']) !!}">View</a>
      @else
        -
      @endif
    </td>
    <td>{{$order['note']}}</td>
    <td class="text-center">
      @if($order['status']==2)
        <b style="color: green">Confirmed</b>
      @else 
        <button type="button" class="btn btn-primary btn-confirm" data-id="{{$order['id']}}" data-no-order="{{$order['no_order']}}" style="font-size: 13px; padding: 5px 8px;">
          Confirm Order
        </button>
      @endif
    </td>
  </tr>
 @endforeach