<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateKursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kurs', function (Blueprint $table) {
            $table->id();
            $table->float('kurs');
            $table->timestamps();
        });

        // INSERT DEFAULT VALUE
        DB::table('kurs')->insert(
            array(
                'kurs'=>0.1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
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
        Schema::dropIfExists('kurs');
    }
}
