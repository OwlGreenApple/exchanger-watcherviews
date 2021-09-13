<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('seller_id');
            $table->BigInteger('buyer_id')->default(0);
            $table->string('no');
            $table->float('kurs')->default(0);
            $table->string('upload')->nullable();
            $table->integer('coin_fee');
            $table->integer('amount');
            $table->integer('total');
            $table->string('date_buy')->nullable();
            $table->string('buyer_comment')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
