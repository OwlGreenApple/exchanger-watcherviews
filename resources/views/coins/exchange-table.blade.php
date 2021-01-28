@if(count($data) > 0)
  <table id="exchanged_coins" class="table table-striped table-bordered">
    <thead align="center">
      <th>Created</th>
      <th>Duration</th>
      <th>Coins Rate</th>
      <th>Youtube Link</th>
      <th>Views</th>
      <th>Drip</th>
      <th>Total Coins</th>
      <th>Total Views</th>
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
          <td class="text-center">
            @if($row->process == $row->drip && $row->process !== null)
                Complete
            @elseif($row->process !== $row->drip && $row->process !== null)
                <span class="text-primary">Process</span>
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