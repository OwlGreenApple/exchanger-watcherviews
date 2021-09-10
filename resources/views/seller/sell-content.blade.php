@if(count($data) > 0)
 <table class="table" id="sell_list">
    <thead>
        <th>Tanggal</th>
        <th>Invoice</th>
        <th>Nama Pembeli</th>
        <th>Total Coin</th>
        <th>Coin Fee</th>
        <th>Kurs</th>
        <th>Harga</th>
        <th>Tanggal Beli</th>
        <th>&nbsp;</th>
    </thead>
    <tbody>        
        @foreach($data as $row)
            <tr>
                <td>{{ $row['created_at'] }}</td>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['buyer'] }}</td>
                <td>{{ $row['amount'] }}</td>
                <td>{{ $row['coin_fee'] }}</td>
                <td>{{ $row['kurs'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['date_buy'] }}</td>
                <td class="text-center">{!! $row['status'] !!}</td>
            </tr>
    @endforeach
        </tbody>
    </table>
@endif

<script type="text/javascript">
    $(function(){
        data_table();
    });

    function data_table()
    {
        $("#sell_list").DataTable({
            "ordering": false
        });
    }
</script>