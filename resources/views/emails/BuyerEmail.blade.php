@if($url == null)
	Maaf, request order anda dengan no invoice : <b>{{ $invoice }}</b><br>
	telah di ditolak oleh seller
	<br>
	<br>
	Di-mohon agar anda mencari penjual yang lain di market.
	<br>
	<br>
	Terima kasih, 
	<br>
	Team Exchanger
@else
	Selamat, request order anda dengan no invoice : <b>{{ $invoice }}</b><br>
	telah di setujui oleh seller
	<br>
	<br>
	Harap <b>dicatat</b> :  Apabila anda tidak konfirmasi pembayaran dalam 6 jam, maka order ini akan dianggap batal.
	<br>
	<br>
	Silahkan login di sini untuk konfirmasi
	<br>
	{!! $url !!} 
	<br>
	<br> 
	Terima kasih, 
	<br>
	Team Exchanger
@endif