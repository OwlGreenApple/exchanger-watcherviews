Invoice anda : <b>{{ $invoice }}</b>
<br>
telah di dispute
<br>
<br>

@if($role == 1)
        Jika anda yakin bahwa anda sudah membayar penjual, maka silahkan menyanggah disini : 
@else
        Jika anda belum konfirmasi silahkan konfimasi di sini : 
        <br/>
        <br/>
        <a href="{!! $url_confirm !!}}">Konfirmasi Penjualan</a>
        <br/>
        <br/>
        Jika anda yakin bahwa pembeli belum membayar anda, maka silahkan menyanggah disini :
@endif
<br/>
<br/>
<a href="{!! $url_dispute !!}}">Dispute</a>
<br>
<br>
Terima kasih, 
<br>
Team Watchermarket