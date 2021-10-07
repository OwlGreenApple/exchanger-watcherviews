@if($trans_id == null)
	Selamat, coin anda dengan no invoice : <b>{{ $invoice }}</b><br>
	telah di order
	<br>
	<br>
	Anda dapat menerima / menolak request order ini.
	<br>
	Harap <b>dicatat</b> : Apabila anda tidak merespon entah itu <b>menerima</b> atau <b>menolak</b> dalam 1x24 jam, maka system akan menganggap anda <b>menerima</b> order tersebut.
	<br>
	<br>
	Silahkan login di sini untuk merespon :
	<br>
	{!! $url !!} 
@else
	Mohon perhatian,
	<br>
	<br>
	pembeli coin anda dengan no invoice <b>{{ $invoice }}</b>.
	<br/>
	telah upload bukti bayar
	<br>
	<br>
	<b>Harap</b> segera di konfirmasi di sini :
	<br>
	{!! $url !!} 
@endif

<br>
<br> 
Terima kasih, 
<br>
Team Exchanger