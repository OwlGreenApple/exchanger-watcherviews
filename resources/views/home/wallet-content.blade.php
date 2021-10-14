<table class="display responsive nowrap w-100" id="data_transaction">
  <thead>
    <th>No</th>
    <th>{{Lang::get('transaction.type')}}</th>
    <th>{{Lang::get('transaction.amount')}}</th>
    <th>{{Lang::get('transaction.trans.fee')}}</th>
    <th>{{Lang::get('transaction.total')}}</th>
    <th>{{Lang::get('transaction.created')}}</th>
  </thead>
  <tbody>
    @if($data->count() > 0)
      @php $no = 1; @endphp
      @foreach($data as $row)
         <tr>
           <td>{{ $no++ }}</td>
           <td>
              @if($row->type == 1)
                {{ Lang::get('transaction.wallet.withdraw') }}
              @elseif($row->type == 2)
                {{ Lang::get('transaction.wallet.send') }}
              @else
                {{ Lang::get('transaction.wallet.return') }}
              @endif
            </td>
           <td>{{ str_replace(",",".",number_format($row->coin)) }}</td>
           <td>{{ str_replace(",",".",number_format($row->fee)) }}</td>
           <td>{{ str_replace(",",".",number_format($row->coin + $row->fee)) }}</td>
           <td>{{ $row->created_at }}</td>
         </tr>
      @endforeach
    @endif
  </tbody>
</table>