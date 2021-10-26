<table id="seller" class="table table-hover shopping-cart-wrap bg-white card-body">
  <thead class="text-muted">
    <tr>
      <th scope="col">{{ Lang::get('transaction.seller') }}</th>
      <th scope="col">{{ Lang::get('transaction.star') }}</th>
      <th scope="col">{{ Lang::get('transaction.comments') }}</th>
      <th scope="col">{{ Lang::get('transaction.qty') }}</th>
      <th scope="col">{{ Lang::get('transaction.price') }}</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
      @if(count($data) > 0)
        @if($find !== null)
          <tr><td colspan="7"><div class="alert alert-secondary">{{ Lang::get('transaction.no_transaction_find') }}<b>{{ $find }}</b> Coin</div></td></tr>
        @endif
        @foreach($data as $row)
        <tr>
            <td data-th="{{ Lang::get('transaction.seller') }}"><span class="title text-truncate"><div class="username">{{ $row['seller_name'] }}</div></span>
            </td>
            <td data-th="{{ Lang::get('transaction.star') }}">
              @if($row['rate'] == 0)
                -
              @else
                @if($row['rate'] > 0)
                @for($x=1;$x<=$row['rate'];$x++)
                  <i class="fas fa-star"></i>
                  @if($x == $row['rate'] && $row['rate'] < 5)
                    @if($row['star_float'] == 0)
                      <i class="fas fa-star"></i>
                    @else
                      <i class="fas fa-star-half"></i>
                    @endif
                  @endif
                @endfor
                @endif
              @endif
            </td>
            <td data-th="{{ Lang::get('transaction.comments') }}"><a href="{!! $row['link'] !!}"><i class="far fa-envelope"></i></a></td>
            <td data-th="{{ Lang::get('transaction.qty') }}">{!! $row['coin'] !!}</td>
            <td data-th="{{ Lang::get('transaction.price') }}"><span class="price-wrap text-success"><b>{{ $row['price'] }}</b></span>
            </td>
            <td data-th="Action"> 
            @if($row['seller'] == auth()->user()->id)
              -
            @else
              <a href="{{ url('buy-detail') }}/{{ $row['id'] }}" data-id="{{ $row['id'] }}" class="btn btn-outline-success btn-sm conf request_buy" data-toggle="tooltip" data-original-title="Save to Wishlist">{{ Lang::get('transaction.detail') }}</a>
            @endif 
            </td>
        </tr>
        @endforeach
      @else
        <tr><td colspan="7"><div class="alert alert-secondary">{{ Lang::get('transaction.no_transaction') }}</div></td></tr>
      @endif
  </tbody>
</table>

<div class="paging" style="overflow-x: auto">
  {{ $paginate }}
</div>