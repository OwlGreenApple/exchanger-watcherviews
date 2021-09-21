<table id="dispute_list" class="table">
  <thead>
    <th>No</th>
    <th>Tanggal Beli</th>
    <th>Invoice</th>
    <th>Pembeli</th>
    <th>Penjual</th>
    <th>Chat</th>
    <th>Keputusan</th>
  </thead>
  <tbody>
    @if($data->count() > 0)
      @php $no = 1 @endphp
      @foreach($data as $row)
        <tr>
          <td>{{ $no++ }}</td>
          <td>{{ $row->date_buy }}</td>
          <td>{{ $row->invoice }}</td>
          <td>
            @if($row->buyer_id > 0)
              <button type="button" data-identity="{!! Storage::disk('s3')->url($row->upload_identity) !!}" data-proof="{!! Storage::disk('s3')->url($row->buyer_proof) !!}" data-mutation="{!! Storage::disk('s3')->url($row->upload_mutation) !!}" data-name="{{ $row->buyer_name }}" date-dispute="{{ $row->buyer_dispute_date }}" class="btn btn-success btn-sm detail" role="1">Detail</button>
            @else
              -
            @endif
          </td>
          <td>
            @if($row->seller_id > 0)
              <button type="button" data-proof="{!! Storage::disk('s3')->url($row->seller_proof) !!}" data-name="{{ $row->seller_name }}" date-dispute="{{ $row->seller_dispute_date }}" class="btn btn-primary btn-sm detail" role="2">Detail</button>
            @else
              -
            @endif
          </td>
          <td><button data-tr-id="{{ $row->id }}" type="button" class="btn btn-default btn-sm"><i class='far fa-comments'></i>&nbsp;Chat</button></td>
          <td>
            @if($row->buyer_id > 0)
              <button type="button" class="btn btn-primary btn-sm">Pembeli Menang</button>
            @else
              <button type="button" data-id="{{ $row->buyer_id }}" data-invoice="{{ $row->invoice }}" class="btn btn-outline-primary btn-sm notify">Notifikasi Pembeli</button>
            @endif
            @if($row->seller_id > 0)
              <button type="button" class="btn btn-success btn-sm">Penjual Menang</button>
            @else
              <button type="button" data-id="{{ $row->seller_id }}" data-invoice="{{ $row->invoice }}" class="btn btn-outline-success btn-sm notify">Notifikasi Penjual</button>
            @endif
            <button type="button" class="btn btn-warning btn-sm">Dispute selesai</button>
          </td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>