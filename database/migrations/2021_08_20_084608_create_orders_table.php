<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('user_id');
            $table->string('no_order');
            $table->string('package');
            $table->Integer('price');
            $table->Integer('total_price');
            $table->string('desc')->nullable(); 
            $table->string('notes')->nullable(); 
            $table->string('proof')->nullable(); 
            $table->string('date_confirm')->nullable(); 
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
        Schema::dropIfExists('orders');
    }
}
