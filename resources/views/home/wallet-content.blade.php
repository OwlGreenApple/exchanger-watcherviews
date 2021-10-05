<table class="table table-bordered w-100" style="font-size : 0.65rem" id="data_transaction">
  <thead class="alert-warning">
    <th class="menu-nomobile">
     No
    </th>
    <th class="menu-nomobile">
      {{Lang::get('transaction.type')}}
    </th>
    <th class="menu-nomobile">
      {{Lang::get('transaction.amount')}}
    </th>
    <th class="menu-nomobile">
      {{Lang::get('transaction.trans.fee')}}
    </th>
    <th class="menu-nomobile">
      {{Lang::get('transaction.total')}}
    </th>
    <th class="menu-nomobile">
      {{Lang::get('transaction.created')}}
    </th>
  </thead>
  <tbody>
    @if($data->count() > 0)
      @php $no = 1; @endphp
      @foreach($data as $row)
         <tr>
           <td class="text-center">{{ $no++ }}</td>
           <td class="text-center">
              @if($row->type == 1)
                {{ Lang::get('transaction.wallet.withdraw') }}
              @elseif($row->type == 2)
                {{ Lang::get('transaction.wallet.send') }}
              @else
                {{ Lang::get('transaction.wallet.return') }}
              @endif
            </td>
           <td class="text-right">{{ str_replace(",",".",number_format($row->coin)) }}</td>
           <td class="text-right">{{ str_replace(",",".",number_format($row->fee)) }}</td>
           <td class="text-right">{{ str_replace(",",".",number_format($row->coin + $row->fee)) }}</td>
           <td class="text-center">{{ $row->created_at }}</td>
         </tr>
      @endforeach
    @endif
  </tbody>
</table>