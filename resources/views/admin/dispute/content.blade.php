<table id="dispute_list" class="table">
  <thead>
    <th>No</th>
    <th>Nama</th>
    <th>Peran</th>
    <th>No Transaksi</th>
    <th>Bukti Identitias</th>
    <th>Bukti Pembayaran</th>
    <th>Bukti Mutasi</th>
    <th>Komentar</th>
    <th>Status</th>
  </thead>
  <tbody>
    @if($data->count() > 0)
      @php $no = 1 @endphp
      @foreach($data as $row)
        <tr>
          <td>{{ $no++ }}</td>
          <td>{{ $row->name }}</td>
          <td>{{ $row->roles }}</td>
          <td>{{ $row->invoice }}</td>
          <td>
            @if($row->upload_identity == null)
              -
            @else
             <a class="popup-newWindow" href="{!! Storage::disk('s3')->url($row->upload_identity) !!}">Lihat identitas</a>
            @endif
          </td>
          <td>
            @if($row->upload_proof == null)
              -
            @else
             <a class="popup-newWindow" href="{!! Storage::disk('s3')->url($row->upload_proof) !!}">Lihat Bukti Bayar</a>
            @endif
          </td>
          <td>
            @if($row->upload_mutation == null)
              -
            @else
             <a class="popup-newWindow" href="{!! Storage::disk('s3')->url($row->upload_mutation) !!}">Lihat Bukti Mutasi</a>
            @endif
          </td>
          <td>
            @if($row->comments == null)
              -
            @else
             <a data-read="{{ $row->comments }}">Lihat Komentar</a>
            @endif
          </td>
          <td>{{ $row->status }}</td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>