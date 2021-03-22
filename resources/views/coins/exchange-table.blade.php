@if(count($data) > 0)
  <table id="exchanged_coins" class="table table-striped table-bordered">
    <thead align="center">
      <th>Tanggal</th>
      <th>Durasi</th>
      <th>Rate Koin</th>
      <th>Youtube Link</th>
      <th>Views</th>
      <th>Drip</th>
      <th>Total Coins</th>
      <th>Total Views</th>
      <th>YT Sebelum</th>
      <th>YT Sesudah</th>
      <th>Status Refill</th>
      <th>Jenis Refill</th>
      <th>Status Drip</th>
      <th>Status</th>
    </thead>
    <tbody>
      @foreach($data as $row)
        <tr>
          <td class="text-center">{{ $row->created_at }}</td>
          <td class="text-center" style="width:5%">{{ $row->duration }}</td>
          <td class="text-center" style="width:10%">{{ number_format($row->coins_value) }}/1000 views</td>
          <td class="text-center" style="width:10%">{{ $row->yt_link }}</td>
          <td class="text-center" style="width:20%">{{ number_format($row->views) }}</td>
          <td class="text-center" style="width:10%">
            @if($row->drip > 0)
              {{ number_format($row->drip) }}
            @else
              -
            @endif
          </td>
          <td class="text-center" style="width:20%">{{ number_format($row->total_coins) }}</td>
          <td class="text-center" style="width:20%">{{ number_format($row->total_views) }}</td>
          <td class="text-center" style="width:20%">{{ number_format($row->yt_before) }}</td>
          <td class="text-center" style="width:20%">{{ number_format($row->yt_after) }}</td>
          <td class="text-center">
            @if($row->refill_btn == 1 && $row->process == $row->drip && $row->process !== null)
              <a id="{{ $row->id }}" class="btn btn-custom btn-sm refil_act">Refill</a>
            @elseif($row->refill_btn == 1 && $row->drip == 0)
              <a id="{{ $row->id }}" class="btn btn-custom btn-sm refil_act">Refill</a>
            @elseif($row->refill_btn == 2)
              Waiting
            @else
              -
            @endif
          </td>
          <td>
            @if($row->refill== 1)
              Manual Refill
            @elseif($row->refill == 2)
              Auto Refill
            @else
              -
            @endif
          </td>
          <td class="text-center">
            @if($row->process == $row->drip && $row->process !== null)
                Complete
            @elseif($row->process !== $row->drip && $row->process !== null)
                <span class="text-primary">Process</span>
            @else 
                -
            @endif
          </td>
          <td>
            @if($row->progress == 0)
              <span class="text-custom">Process</span>
            @elseif($row->progress == 1)
              <b class="text-success">Complete</b>
            @else
              -
            @endif
          </td>
        </tr>
       @endforeach
    </tbody>
  </table>

   <script type="text/javascript">
    $(function(){
      $("#exchanged_coins").DataTable({
        "lengthMenu": [ 5, 10, 25, 50, 75, 100, 250, 500 ],
        "pageLength" : 5,
        "aaSorting" : [],
      });
    });
   </script>
@endif