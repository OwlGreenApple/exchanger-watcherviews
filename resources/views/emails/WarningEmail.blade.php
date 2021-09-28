@if($pos !== null)

Sehubungan dengan dispute invoice : <b>{{ $invoice }}</b>
<br>
maka admin mengundang anda untuk menyelesaikan dispute ini melalui chat
<br>
dengan link di bawah ini :
<br>
<br>
<a href="{{ url('chat') }}/{{ $pos }}">Chat Room</a>
<br>
<br>
Terima Kasih
<br>
Team Exchanger

@else

Mohon perhatian
<br>
sehubungan dengan dispute invoice : <b>{{ $invoice }}</b> maka akun anda telah mendapatkan warning.
<br>
<br>
Jika anda mendapatkan <b>warning</b> sebanyak 2 kali
<br/>
maka akun anda akan di-suspend , sehingga anda tidak dapat melakukkan segala aktifitas di situs kami selama 1 minggu.
<br/>
<br/>
Apabila anda terkena <b>suspend</b> sebanyak 2 kali
<br>
maka akun anda akan di-non-aktifkan.
<br>
<br>
Mohon perhatian dan kerja sama dari anda.
<br>
<br>
Terima kasih, 
<br>
Team Exchanger

@endif