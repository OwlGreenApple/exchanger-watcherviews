<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsFromExchanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->string('yt_link')->after('id_exchange');
            $table->Integer('views')->default(0)->after('yt_link');
            $table->Integer('drip')->default(0)->after('views');
            $table->BigInteger('total_coins')->default(0)->after('drip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('exchanges',[ 'yt_link','views','drip','total_coins']))
        {
            Schema::table('exchanges', function (Blueprint $table) {
              $table->dropColumn('yt_link');
              $table->dropColumn('views');
              $table->dropColumn('drip');
              $table->dropColumn('total_coins');
            });
        }
    }
}
