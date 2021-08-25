<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->text('notif_order');
            $table->text('notif_after');
            $table->BigInteger('admin_id');
            $table->timestamps();
        });
        $notif_order = $notif_after = "";

        $notif_order .= "Terimakasih atas order anda,\n";
        $notif_order .= "Detail order anda : \n\n";
        $notif_order .= "No Order : *[NO-ORDER]*\n";
        $notif_order .= "Paket : [PACKAGE]\n";
        $notif_order .= "Harga : Rp.[PRICE]\n";
        $notif_order .= "Jumlah View : [PURCHASE]\n";
        $notif_order .= "Total : Rp.*[TOTAL]*\n\n";
        $notif_order .= "Transfer ke BCA :\n";
        $notif_order .= "Langkah sesudah transfer :\n";
        $notif_order .= "No WA customer service : \n\n";
        $notif_order .= "Terima Kasih.";

        $notif_after .= "Harap segera melakukan pembayaran atas order anda : \n\n";
        $notif_after .= "No Order : *[NO-ORDER]*\n";
        $notif_after .= "Paket : [PACKAGE]\n";
        $notif_after .= "Harga : Rp.[PRICE]\n";
        $notif_after .= "Jumlah View : [PURCHASE]\n";
        $notif_after .= "Total : Rp.*[TOTAL]*\n\n";
        $notif_after .= "Transfer ke BCA :\n";
        $notif_after .= "Langkah sesudah transfer :\n";
        $notif_after .= "No WA customer service : \n\n";
        $notif_after .= "Terima Kasih.";

        // INSERT DEFAULT VALUE
        DB::table('notification')->insert(
            array(
                'notif_order' => $notif_order,
                'notif_after' => $notif_after,
                'admin_id' => 8,
                'created_at' => Date("Y-m-d H:i:s"),
                'updated_at' => Date("Y-m-d H:i:s"),
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
