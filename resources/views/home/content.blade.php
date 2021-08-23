<div class="paging">
  {{ $pager }}
</div>

@if($orders->count() > 0)
<div class="table-responsive">
<table class="table table-condensed bg-white" style="width : 100%" id="myTable">
    <thead align="center">
      <th class="menu-mobile">
        {{$lang::get('order.detail')}}
      </th>
      <th class="menu-nomobile" action="no_order">
       {{$lang::get('order.no')}}
      </th>
      <th class="menu-nomobile" action="package">
        {{$lang::get('order.package')}}
      </th>
      <th class="menu-nomobile" action="discount">
        {{$lang::get('order.yt_thumb')}}
      </th>
      <th class="menu-nomobile" action="discount">
        {{$lang::get('order.yt_url')}}
      </th>
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.price')}}
      </th>
      <th class="menu-nomobile w-25">
        {{$lang::get('order.total')}}
      </th>
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.purchased_view')}}
      </th> 
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.start')}}
      </th>
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.views')}}
      </th>
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.date')}}
      </th>
      <th class="menu-nomobile" style="width:15%">
        {{$lang::get('order.date_complete')}}
      </th>
      <th class="menu-nomobile" style="width:200px">
        {{$lang::get('order.proof')}}
      </th>
      <th class="header" action="status">
        {{$lang::get('order.status')}}
      </th>
    </thead>

    <tbody>
      @php $no = 1 @endphp
      @foreach($orders as $order)
        <tr>
          <td class="menu-nomobile" data-label="No Order">
            {{$order->no_order}}  
          </td>
          <td class="menu-nomobile" data-label="Package">
            {{$order->package}}
          </td> 
          <td class="menu-nomobile" data-label="link">
            <img width="80" height="45" src="https://img.youtube.com/vi/{{ $extract->extract_youtube_value($order->link) }}/0.jpg" />
          </td>
          <td class="menu-nomobile" data-label="link">
            {{ $order->link }}
          </td>
          <td class="menu-nomobile" data-label="Harga">
            Rp. <?php echo number_format($order->price) ?>
          </td>
          <td class="menu-nomobile" data-label="Total">
            Rp. <?php echo number_format($order->total_price) ?>
          </td>
          <td class="menu-nomobile">
            {{ number_format($order->purchased_views) }}
          </td> 
          <td class="menu-nomobile">
            {{ number_format($order->start_view) }}
          </td> 
          <td class="menu-nomobile">
            {{ number_format($order->views) }}
          </td>
          <td class="menu-nomobile" data-label="Date">
            {{$order->created_at}}
          </td>
          <td class="menu-nomobile">
            @if($order->status==3)
              {{$order->updated_at}}
            @else 
              -
            @endif
          </td>
          <td class="menu-nomobile" data-label="Bukti Bayar" align="center">
            @if($order->proof == null)
                <button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#confirm-payment" data-id="{{$order->id}}" data-no-order="{{$order->no_order}}" data-package="{{$order->package}}" data-total="{{$order->total_price}}" data-date="{{$order->created_at}}" data-purchased-view="{{$order->purchased_views}}" style="font-size: 13px; padding: 5px 8px;">
                {{ $lang::get('order.confirm') }}
              </button>
            @else
              <a class="popup-newWindow" href="<?php 
                // echo Storage::disk('public')->url('app/'.$order->buktibayar);
                echo Storage::disk('s3')->url($order->proof);
              ?>">
                View
              </a>
            @endif
          </td>
          <td data-label="Status">
            @if($order->status==1)
              <span style="color: blue"><b>{{ $lang::get('order.process') }}</b></span>
            @elseif($order->status==2)
              <span style="color: orange"><b>{{ $lang::get('order.partial') }}</b></span>
            @elseif($order->status==3)
              <div><span class="badge badge-success px-2 py-2">{{ $lang::get('order.complete') }}</span></div>
              <div class="mt-1"><a class="text-info">Re-Promote</a></div>
            @else 
              <span><b>{{ $lang::get('order.waiting') }}</b></span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
</table>
</div>
@endif

<div class="paging">
  {{ $pager }}
</div>