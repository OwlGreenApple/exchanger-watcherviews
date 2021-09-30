<table id="dispute_list" class="table table-bordered">
  <thead class="bg-light">
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
            @if($row->buyer_dispute_id > 0)
              <button type="button" data-identity="{!! Storage::disk('s3')->url($row->upload_identity) !!}" data-proof="{!! Storage::disk('s3')->url($row->buyer_proof) !!}" data-mutation="{!! Storage::disk('s3')->url($row->upload_mutation) !!}" data-name="{{ $row->buyer_name }}" date-dispute="{{ $row->buyer_dispute_date }}" class="btn btn-success btn-sm detail" role="1">Detail</button>
            @else
              -
            @endif
          </td>
          <td>
            @if($row->seller_dispute_id > 0)
              <button type="button" data-proof="{!! Storage::disk('s3')->url($row->seller_proof) !!}" data-name="{{ $row->seller_name }}" date-dispute="{{ $row->seller_dispute_date }}" class="btn btn-primary btn-sm detail" role="2">Detail</button>
            @else
              -
            @endif
          </td>
          <td>
            <a href="{{ url('chat') }}/{{ $row->id }}" class="btn btn-default btn-sm popup-newWindow"><i class='far fa-comments'></i>&nbsp;Chat</a>&nbsp;

            @if($row->status == 4)
              @if($row->seller_status == 0 || $row->buyer_status == 0)
                &nbsp;
              @else
                <a data-buyer="{{ $row->buyer_id }}" data-seller="{{ $row->seller_id }}" data-tr-id="{{ $row->id }}" class="btn btn-warning btn-sm text-dark bell"><i class='far fa-bell'></i></a>
              @endif
            @endif
        </td>
          @if($row->status == 4)
            <td>
              <!-- BUYER -->
              @if($row->buyer_status > 0)
                @if($row->buyer_dispute_id > 0)
                    <button type="button" data-buyer="{{ $row->buyer_id }}" data-seller="{{ $row->seller_id }}" data-win="1" data-tr-id="{{ $row->id }}" class="btn btn-primary btn-sm blame">Pembeli Menang</button>
                @else
                  <button type="button" data-id="{{ $row->buyer_id }}" data-tr-id="{{ $row->id }}" data-role="1" class="btn btn-outline-primary btn-sm notify">Notifikasi Pembeli</button>
                @endif
              @else
                  <span class="btn text-danger">Pembeli terkenan ban</span>
              @endif
              <!-- SELLER -->
              @if($row->seller_status > 0)
                @if($row->seller_dispute_id > 0)
                  <button type="button" data-buyer="{{ $row->buyer_id }}" data-seller="{{ $row->seller_id }}" data-win="2" data-tr-id="{{ $row->id }}" class="btn btn-success btn-sm blame">Penjual Menang</button>
                @else
                  <button type="button" data-id="{{ $row->seller_id }}" data-tr-id="{{ $row->id }}" data-role="2" class="btn btn-outline-success btn-sm notify">Notifikasi Penjual</button>
                @endif
              @else
                  <span class="btn text-danger">Penjual terkenan ban</span>
              @endif
              <!-- END DISPUTE -->
              @if($row->seller_status == 0 || $row->buyer_status == 0)
                &nbsp;
              @else
                @if(($row->buyer_dispute_id > 0 && $row->seller_dispute_id > 0))
                  <button data-buyer="{{ $row->buyer_id }}" data-seller="{{ $row->seller_id }}" data-win="0" data-tr-id="{{ $row->id }}" type="button" class="btn btn-warning btn-sm blame">Dispute selesai</button>
                @endif
              @endif
            </td>
          @elseif($row->status == 3)
            <td class="text-primary font-weight-bold">Pembeli Menang</td>
          @elseif($row->status == 5)
            <td class="text-success font-weight-bold">Penjual Menang</td>
          @elseif($row->status == 6)
            <td class="text-black-50 font-weight-bold">Diakhiri Admin</td>
          @else
            <td class="text-danger font-weight-bold">Error</td>
          @endif
        </tr>
      @endforeach
    @endif
  </tbody>
</table>