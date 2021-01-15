@if($data->count() > 0)
   @foreach($data as $row)
    <tr>
      <td class="text-center">{{ $row->created_at }}</td>
      <td class="text-center" style="width:5%">{{ $row->duration }}</td>
      <td class="text-center" style="width:30%">{{ number_format($row->coins_value) }}</td>
      <td class="text-center" style="width:20%">{{ number_format($row->total_views) }}</td>
      <td class="text-center">
        @if($row->status == 0)
          <button type="button" class="btn btn-primary btn-allocate btn-sm" data-toggle="modal" data-target="#allocate">
            Allocate Coins
          </button>
        @else 
            <b class="text-success">Allocated</b>
        @endif
      </td>
    </tr>
   @endforeach
@endif