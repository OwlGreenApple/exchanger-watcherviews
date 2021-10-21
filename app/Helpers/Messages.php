<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Messages
{

    //NOTIFICATION WARNING TO WA
    public static function message_warning($invoice)
    {
      $msg ='';
      $msg .='Mohon perhatian'."\n";
      $msg .='sehubungan dengan dispute invoice : *'.$invoice.'* maka akun anda telah mendapatkan *warning*.'."\n\n";

      $msg .='Jika anda mendapatkan *warning* sekali lagi'."\n";
      $msg .='maka akun anda akan di-suspend , sehingga anda tidak dapat melakukan transaksi di situs kami selama 1 minggu.'."\n\n";

      $msg .='Mohon perhatian dan kerja sama dari anda.'."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Watchermarket';

      return $msg;
    }

    //NOTIFICATION SUSPEND TO WA
    public static function message_suspend($invoice)
    {
      $msg ='';
      $msg .='Mohon perhatian'."\n";
      $msg .='Sehubungan dengan dispute invoice : *'.$invoice.'* maka akun anda terkena *suspend*,'."\n\n";

      $msg .='maka saat ini anda tidak dapat melakukkan transaksi di situs kami selama 1 minggu.'."\n\n";

      $msg .='Apabila anda terkena *suspend* sekali lagi'."\n";
      $msg .='maka akun anda akan di-non-aktifkan.'."\n\n";

      $msg .='Mohon perhatian dan kerja sama dari anda.'."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Watchermarket';

      return $msg;
    }

    //NOTIFICATION BANNED TO WA
    public static function message_ban($invoice)
    {
      $msg ='';
      $msg .='Mohon perhatian'."\n";
      $msg .='Sehubungan dengan dispute invoice : *'.$invoice.'* maka akun anda di *non-aktifkan*.'."\n\n";

      $msg .='Maka dengan demikian anda tidak dapat melakukan segala aktifitas di situs kami,'."\n";
      $msg .='karena menurut system kami anda telah melanggar syarat dan ketentuan berulang kali.'."\n\n";
      // $msg .='Apabila anda merasa tidak melanggar ketentuan kami anda bisa mengontak admin kami.'."\n\n";

      $msg .='Mohon pengertian anda.'."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Watchermarket';

      return $msg;
    }

     // display on notifcation event
    public static function warning_message($invoice)
    {
      $msg ='';
      $msg .='Mohon perhatian'."<br>";
      $msg .='Sehubungan dengan dispute invoice : <b>'.$invoice.'</b> maka akun anda telah mendapatkan <b>warning</b>.'."<br/><br/>";

      $msg .='Apabila anda terkena <b>warning</b> sekali lagi,'."<br/>";
      $msg .='maka akun anda akan di-<b>suspend</b> sehingga anda tidak dapat melakukkan transaksi selama 1 minggu.'."<br/><br/>";

      $msg .='Mohon perhatian dan kerja sama dari anda.'."<br/><br/>";
      $msg .='Terima Kasih'."<br/>";
      $msg .='Team Watchermarket';

      return $msg;
    }

    public static function suspend_message($invoice)
    {
      $msg ='';
      $msg .='Mohon perhatian'."<br>";
      $msg .='Sehubungan dengan dispute invoice : <b>'.$invoice.'</b> maka akun anda telah ter-<b>suspend</b>.'."<br/><br/>";

      $msg .='Maka dengan demikian anda tidak dapat melakukkan transaksi di situs kami selama 1 minggu.'."<br/><br/>";

      $msg .='Apabila anda terkena <b>suspend</b> sekali lagi,'."<br/>";
      $msg .='maka akun anda akan di-<b>non-aktifkan</b>.'."<br/><br/>";

      $msg .='Mohon perhatian dan kerja sama dari anda.'."<br/><br/>";
      $msg .='Terima Kasih'."<br/>";
      $msg .='Team Watchermarket';

      return $msg;
    }

    public static function buyer_notification($invoice,$trans_id = null,$coin = null, $total = null)
    {
      if($trans_id == null)
      {
        $msg ='';
        $msg .='Maaf, request order anda dengan no invoice *'.$invoice.'*'."\n";
        $msg .='telah di tolak oleh seller'."\n\n";
        $msg .='Di-mohon agar anda mencari penjual yang lain di market.'."\n\n";
        $msg .='Terima Kasih'."\n";
        $msg .='Team Watchermarket';
      }
      else
      {
        $msg ='';
        $msg .='Selamat, request order anda dengan no invoice *'.$invoice.'*'."\n";
        $msg .='telah di setujui oleh seller,'."\n\n";
        $msg .='Jumlah Coin : *'.$coin.'*,'."\n";
        $msg .='Total : *'.$total.'*,'."\n";
        $msg .='Segera bayar order anda di link berikut : '.url('deal').'/'.$trans_id.''."\n";
        $msg .='*Perhatian* : Apabila anda tidak konfirmasi pembayaran dalam 6 jam, maka order ini akan dianggap batal.'."\n\n";
        $msg .='Terima Kasih'."\n";
        $msg .='Team Watchermarket';
      }

      return $msg;
    }

    public static function seller_notification($invoice,$tr_id = null,$coin = null, $total = null)
    {
      $msg ='';
      if($tr_id == null)
      {
        $msg .='Selamat, coin anda dengan no invoice *'.$invoice.'*'."\n";
        $msg .='telah di order'."\n\n";
        $msg .='Jumlah Coin : *'.$coin.'*'."\n";
        $msg .='Total : *'.$total.'*'."\n\n";
        $msg .='Anda dapat menerima / menolak request order ini.'."\n";
        $msg .='*Harap dicatat* : Apabila anda tidak merespon entah itu *menerima* atau *menolak* dalam 1x24 jam, maka system akan menganggap anda *menerima* order tersebut.'."\n\n";
        $msg .='Silahkan login di sini untuk merespon :'."\n";
        $msg .=url('sell')."\n\n";
        $msg .='Terima Kasih'."\n";
        $msg .='Team Watchermarket';
      }
      else
      {
        $msg .='Mohon perhatian,'."\n\n";
        $msg .='pembeli coin anda dengan no invoice *'.$invoice.'*'."\n";
        $msg .='telah upload bukti bayar'."\n\n";
        $msg .='Jumlah Coin : *'.$coin.'*'."\n";
        $msg .='Total : *'.$total.'*'."\n";
        $msg .='*Harap* segera di konfirmasi di sini :'."\n";
        $msg .=url('sell-confirm').'/'.$tr_id."\n\n";
        $msg .='Terima Kasih'."\n";
        $msg .='Team Watchermarket';
      }

      return $msg;
    }

    public static function forgot($password,$name)
    {
      $msg ='';
      $msg .='Halo '.$name.','."\n\n";
      $msg .='Anda telah me-reset password anda, password anda yang baru adalah :'."\n";
      $msg .='*'.$password.'*'."\n\n";
      $msg .='Jika anda memerlukan bantuan'."\n";
      $msg .='*Silahkan kontak customer kami*'."\n";
      $msg .='Telegram : @activomni_cs'."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Watchermarket';

      return $msg;
    }

    public static function registered($password,$name)
    {
      $msg ='';
      $msg .='Halo '.$name.','."\n\n";
      $msg .='Selamat datang di Watchermarket,'."\n";
      $msg .='*Password anda adalah* : '.$password.' '."\n\n";
      $msg .='*Link login:*'."\n";
      $msg .=url('/').'/login'."\n\n";
      $msg .='Jika anda memerlukan bantuan'."\n";
      $msg .='*Silahkan kontak customer kami*'."\n";
      $msg .='*Telegram* :  @activomni_cs'."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Watchermarket';
      return $msg;
    }
/*end of class*/
}