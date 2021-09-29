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
      Biaya Transaksi
    </th>
    <th class="menu-nomobile">
      Total
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
              @else
                {{ Lang::get('transaction.wallet.send') }}
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