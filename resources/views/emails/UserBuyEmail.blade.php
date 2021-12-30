@if($case !== null)

Terima kasih,
<br>
Admin telah MENERIMA KONFIRMASI PEMBAYARAN anda
<br>
berikut ini adalah rincian ORDER yang telah dikonfirmasi admin
<br>
<br>
No Order : <b>{{ $order->no_order }}</b>
<br>
Nama : <b>{{$case}}</b>
<br>
Status Order : Confirmed
<br>
Anda telah memesan paket <b>{{ $order->package }}</b> <b>Rp.{{ $pc->pricing_format($order->total_price) }}</b>
<br>
<br>
Aktivasi telah sukses dilakukan. Anda sudah bisa menggunakan layanan Watchermarket. Segera hubungi kami jika masih ada ditanyakan di <a href="https://omli.club/watchermarket">https://omli.club/watchermarket</a>
<br>
<br>
Salam hangat,
<br>
Watchermarket

@else

Mohon Perhatian
<br>
<br>
Ada user yang melakukkan konfirmasi pembayaran dengan rincian:
<br>
<br>
No Order : <b>{{ $order->no_order }}</b>
<br>
Paket : <b>{{ $order->package }}</b>
<br>
Harga : <b>{{ $pc->pricing_format($order->price) }}</b>
<br>
Total Harga : <b>Rp.{{ $pc->pricing_format($order->total_price) }}</b>
<br>
<br>
Harap segera di cek.
<br>
<br>
Terima kasih

@endif