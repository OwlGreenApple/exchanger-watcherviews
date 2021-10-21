@if($url == null)
	Maaf, request order anda dengan no invoice : <b>{{ $invoice }}</b><br>
	telah di ditolak oleh seller
	<br>
	<br>
	Di-mohon agar anda mencari penjual yang lain di market.
@else
	Selamat, request order anda dengan no invoice : <b>{{ $invoice }}</b><br>
	telah di setujui oleh seller,
	<br>
	<br>
	Jumlah Coin : <b>{{ $coin }}</b>
	<br/>
	Total : {{ Lang::get('custom.curency') }} <b>{{ $total }}</b>
	<br/>
	Segera bayar order anda di link berikut : {!! $url !!} 
	<br>
	<br> 
	<b>Perhatian</b> :  Apabila anda tidak konfirmasi pembayaran dalam 6 jam, maka order ini akan dianggap batal.
@endif

<br>
<br>
Terima kasih, 
<br>
Team Watchermarket