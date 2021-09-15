<table class="table" id="selling">
    <thead>
        <th>Tanggal</th>
        <th>Invoice</th>
        <th>Penjual</th>
        <th>Total Coin</th>
        <th>Kurs</th>
        <th>Harga</th>
        <th>Action</th>
        <th>Penilaian</th>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach($data as $key=>$row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['seller'] }}</td>
                    <td>{{ $row['coin'] }}</td>
                    <td>{{ $row['kurs'] }}</td>
                    <td>{{ $row['price'] }}</td>
                    <td>{!! $row['status'] !!}</td>
                    <td>{!! $row['comments'] !!}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<!-- 
        <tr>
            <td>2021-09-02</td>
            <td>B-210902-001</td>
            <td>500.000</td>
            <td>0.1</td>
            <td>Rp 50.000</td>
            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
            <td>
                <a target="_blank" href="{{ url('buyer-confirm') }}" class="btn btn-primary btn-sm">Konfirmasi</a>
            </td>
        </tr>
        <tr>
            <td>2021-09-02</td>
            <td>B-210903-001</td>
            <td>100.000</td>
            <td>0.1</td>
            <td>Rp 10.000</td>
            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
            <td>
                <a target="_blank" href="{{ url('buyer-dispute') }}" class="btn btn-danger btn-sm">Dispute</a>
            </td>
        </tr>
        <tr>
            <td>2021-09-02</td>
            <td>B-210903-001</td>
            <td>100.000</td>
            <td>0.1</td>
            <td>Rp 10.000</td>
            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
            <td>
                <span class="btn alert-warning">Proses</span>
            </td>
        </tr>
        <tr>
            <td>2021-09-02</td>
            <td>B-210903-001</td>
            <td>100.000</td>
            <td>0.1</td>
            <td>Rp 10.000</td>
            <td><a href="{{ url('comments') }}"><i class="far fa-envelope"></i></a></td>
            <td>
                <span class="btn alert-success">Lunas</span>
            </td>
        </tr>
     -->