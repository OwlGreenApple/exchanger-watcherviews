<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColProgessFromExchanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->boolean('progress')->default(0)->after('status_view');
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
            $table->boolean('progress');
        });
    }
}
