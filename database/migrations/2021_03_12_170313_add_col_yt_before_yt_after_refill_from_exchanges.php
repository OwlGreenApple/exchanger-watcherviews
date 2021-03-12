<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColYtBeforeYtAfterRefillFromExchanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->Integer('yt_before')->default(0)->after('total_views');
            $table->Integer('yt_after')->default(0)->after('yt_before');
            $table->boolean('refill')->default(0)->after('yt_after');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropColumn('yt_before');
            $table->dropColumn('yt_after');
            $table->dropColumn('refill');
        });
    }
}
