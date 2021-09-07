<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('watcherviews_id')->default(0);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_no')->nullable();
            $table->string('ovo')->nullable();
            $table->string('dana')->nullable();
            $table->string('gopay')->nullable();
            $table->BigInteger('coin')->default(0);
            $table->string('membership')->default('free');
            $table->boolean('trial')->default(3);
            $table->string('end_membership')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('gender');
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
