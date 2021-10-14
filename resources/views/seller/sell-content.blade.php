@if(count($data) > 0)
 <table class="display responsive nowrap" id="sell_list">
    <thead>
        <th>Invoice</th>
        <th>Tanggal</th>
        <th>Pembeli</th>
        <th>Total Coin</th>
        <th>Coin Fee</th>
        <th>Kurs</th>
        <th>Harga</th>
        <th>Tanggal Beli</th>
        <th>Pembayaran</th>
        <!-- <th>Trial</th> -->
        <th>Rate Pembeli</th>
        <th>&nbsp;</th>
    </thead>
    <tbody>        
        @foreach($data as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['created_at'] }}</td>
                <td>{{ $row['buyer'] }}</td>
                <td>{{ $row['amount'] }}</td>
                <td>{{ $row['coin_fee'] }}</td>
                <td>{{ $row['kurs'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['date_buy'] }}</td>
                <td>{{ $row['payment'] }}</td>
                <td>{!! $row['rate'] !!}</td>
                <td class="text-center">{!! $row['status'] !!}</td>
            </tr>
    @endforeach
        </tbody>
    </table>
@endif