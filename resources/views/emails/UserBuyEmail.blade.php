Mohon Perhatian
<br>
<br>
Ada user yang melakukkan konfirmasi pembayaran dengan detil:
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