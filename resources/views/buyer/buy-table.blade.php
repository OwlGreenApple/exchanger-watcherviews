<table id="seller" class="table table-hover shopping-cart-wrap">
    <thead class="text-muted">
    <tr>
      <th scope="col" width="240">{{ Lang::get('transaction.buy.no') }}</th>
      <th scope="col">{{ Lang::get('transaction.qty') }}</th>
      <th scope="col">{{ Lang::get('transaction.price') }}</th>
      <th scope="col">{{ Lang::get('transaction.rate') }}</th>
      <th scope="col">{{ Lang::get('transaction.comments') }}</th>
      <th scope="col" width="120" class="text-right">Action</th>
    </tr>
    </thead>
    <tbody>
    @if(count($data) > 0)
      @foreach($data as $row)
      <tr>
          <td><h6 class="title text-truncate">{{ $row['no'] }}</h6></td>
          <td>{{ $row['coin'] }}</td>
          <td> 
              <div class="price-wrap"> 
                  <var class="price">{{ $row['price'] }}</var> 
                 <!--  <small class="text-muted">(USD5 each)</small>  -->
              </div> <!-- price-wrap .// -->
          </td>
          <td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"><i class="fas fa-star"></td>
          <td><i class="far fa-envelope"></i></td>
          <td class="text-right"> 
          @if($row['seller'] == auth()->user()->id)
            -
          @else
            <a href="{{ url('buy-detail') }}/{{ $row['no'] }}" class="btn btn-outline-success btn-sm conf" data-toggle="tooltip" data-original-title="Save to Wishlist">Beli</a>
          @endif 
          </td>
      </tr>
      @endforeach
    @endif
    </tbody>
</table>