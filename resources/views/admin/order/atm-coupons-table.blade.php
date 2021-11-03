<table class="table" id="coupon_table">
    <thead>
      <th>No</th>
      <th>Kode kupon</th>
      <th>Voucher</th>
      <th>Tanggal Dibuat</th>
      <th>Status</th>
      <th>Nama</th>
      <th>Tanggal Diambil</th>
    </thead>
    <tbody>
      @if($data->count() > 0)
        @php $no = 1; @endphp

        @foreach($data as $row)
          <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $row->code }}</td>
            <td>@if($row->type == 1) 100.000 @else 200.000 @endif</td>
            <td>{{ $row->created_at }}</td>
             <td>@if($row->is_used == 1) <span class="text-success">Sudah Diambil</span> @else Belum Diambil @endif</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->date_used }}</td>
          </tr>
        @endforeach
      @endif
    </tbody>
</table>