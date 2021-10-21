@if($pos !== null)

Sehubungan dengan dispute invoice : <b>{{ $invoice }}</b>
<br>
maka admin mengundang anda untuk menyelesaikan dispute ini melalui chat
<br>
dengan link di bawah ini :
<br>
<br>
<a href="{{ url('chat') }}/{{ $pos }}">Chat Room</a>

@elseif($subject == 'Warning Email')

Mohon perhatian
<br>
Sehubungan dengan dispute invoice : <b>{{ $invoice }}</b> maka akun anda telah mendapatkan warning.
<br>
<br>
Jika anda mendapatkan <b>warning</b> sekali lagi,
<br/>
maka akun anda akan di-<b>suspend</b> , sehingga anda tidak dapat melakukkan transaksi di situs kami selama 1 minggu.
<br>
<br>
Mohon perhatian dan kerja sama dari anda.

@elseif($subject == 'Suspend Email')

Mohon perhatian
<br>
sehubungan dengan dispute invoice : <b>{{ $invoice }}</b> maka akun anda telah ter-<b>suspend</b>.
<br>
<br>
Maka dengan demikian anda tidak dapat melakukkan transaksi di situs kami selama 1 minggu.
<br/>
<br/>
Apabila anda terkena <b>suspend</b> sekali lagi
<br>
maka akun anda akan di-<b>non-aktifkan</b>.
<br>
<br>
Mohon perhatian dan kerja sama dari anda.

@else

Mohon perhatian
<br>
Sehubungan dengan dispute invoice : <b>{{ $invoice }}</b> maka akun anda telah di-<b>non-aktifkan</b>.
<br>
<br>
Maka dengan demikian anda tidak dapat melakukan segala aktifitas di situs kami, karena menurut system kami anda telah melanggar syarat dan ketentuan berulang kali.
<br/>
<br/>
Mohon pengertian anda.
@endif

<br>
<br>
Terima kasih, 
<br>
Team Watchermarket