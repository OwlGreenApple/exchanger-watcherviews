
<table class="order-column compact stripe" id="contact-table">
    <thead align="center">
      <th>No</th>
      <th width="30%">Judul</th>
      <th>Pesan</th>
    </thead>

    <tbody>
      @if($data->count() > 0)
         @php $no = 1; @endphp
         @foreach($data as $row)
          <tr>
            <td>{{ $no }}</td>
            <td>{{ $row->title }}</td>
            <td class="text-center"><a class="open_message btn btn-primary btn-sm" data-attr="{{ $row->title  }}" data-id="{{ $row->id }}">Lihat Pesan</a></td>
          </tr>
           @php $no++; @endphp
         @endforeach
       @endif
    </tbody>
</table>

<script type="text/javascript">
  $(function(){
    tables();
  });

  function tables()
  {
    $("#contact-table").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : []
    });
  }
</script>