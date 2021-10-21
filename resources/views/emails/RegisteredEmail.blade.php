@if($destination == null)

	Halo {{$name}},
	<br>
	<br>
	Selamat datang di Watchermarket
	<br>
	<strong>Password anda adalah : </strong>: {{ $password }} 
	<br>
	<br>
	<strong>Link login:</strong>
	<br>
	{{ url('/') }}/login 
	<br>
	<br>
	Jika anda memerlukan bantuan
	<br>
	<strong>Silahkan kontak customer kami
	<br>
	Telegram</strong>: @activomni_cs
	<br>
	<br>
	<br> Terima kasih, 
	<br>
	Team Watchermarket<br>
	<span style="font-style: italic;">*Watchermarket adalah bagian dari Activomni.com</span>
	<br>
	<br>
	Jika ada yang ingin ditanyakan,<br>
	Silakan hubungi support kami di info@activomni.com <br>
	Pesan Anda akan kami balas maximal 1x24 jam kerja. <br>
	<br>
	Atau Anda juga bisa menghubungi support kami di WA 0817-318-368 <br>
	Pada jam kerja, Senin s/d Jumat jam 08.00-17.00<br>

@else
	Halo {{$name}},
	<br>
	<br>
	Anda telah me-reset password anda, password anda yang baru adalah : 
	<br>
	<strong>{{ $password }} </strong><br>
	<br>
	<br>
	Jika anda memerlukan bantuan
	<br>
	<strong>Silahkan kontak customer kami
	<br>
	Telegram</strong>: @activomni_cs
	<br>
	<br> 
	Terima kasih, 
	<br>
	Team Watchermarket
@endif


