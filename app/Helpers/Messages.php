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
      $msg .='Team Exchanger';

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
      $msg .='Team Exchanger';

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
      $msg .='Team Exchanger';

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
      $msg .='Team Exchanger';

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
      $msg .='Team Exchanger';

      return $msg;
    }

    public static function seller_notification($invoice)
    {
      $msg ='';
      $msg .='Selamat coin anda dengan no invoice *'.$invoice.'*'."\n";
      $msg .='telah di order'."\n\n";
      $msg .='Anda dapat menerima / menolak request order ini.'."\n";
      $msg .='*Harap dicatat* : Apabila anda tidak merespon entah itu *menerima* atau *menolak* dalam 1x24 jam, maka system akan menganggap anda *menerima* order tersebut.'."\n\n";
      $msg .='Silahkan login di sini untuk merespon :'."\n";
      $msg .=url('sell')."\n\n";
      $msg .='Terima Kasih'."\n";
      $msg .='Team Exchanger';

      return $msg;
    }
/*end of class*/
}