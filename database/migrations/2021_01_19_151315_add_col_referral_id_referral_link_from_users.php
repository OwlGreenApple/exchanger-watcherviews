<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColReferralIdReferralLinkFromUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->BigInteger('referral_id')->default(0)->after('remember_token');
            $table->string('referral_link')->nullable()->after('referral_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if(Schema::hasColumns('users',['referral_id','referral_link'])){
              $table->dropColumn('referral_id');
              $table->dropColumn('referral_link');
            }
        });
    }
}
