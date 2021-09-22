<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldBuyerDisputeSellerDisputeFromTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->BigInteger('buyer_dispute_id')->default(0)->after('date_buy');
            $table->BigInteger('seller_dispute_id')->default(0)->after('buyer_dispute_id');
            $table->dropColumn('buyer_comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('buyer_dispute_id');
            $table->dropColumn('seller_dispute_id');
            $table->text('buyer_comment')->after('date_buy');
        });
    }
}
