<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tides', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date_at');
            $table->string('place_id', 16);
            $table->integer('tide00');
            $table->integer('tide01');
            $table->integer('tide02');
            $table->integer('tide03');
            $table->integer('tide04');
            $table->integer('tide05');
            $table->integer('tide06');
            $table->integer('tide07');
            $table->integer('tide08');
            $table->integer('tide09');
            $table->integer('tide10');
            $table->integer('tide11');
            $table->integer('tide12');
            $table->integer('tide13');
            $table->integer('tide14');
            $table->integer('tide15');
            $table->integer('tide16');
            $table->integer('tide17');
            $table->integer('tide18');
            $table->integer('tide19');
            $table->integer('tide20');
            $table->integer('tide21');
            $table->integer('tide22');
            $table->integer('tide23');

            $table->integer('max_tide1')->nullable();
            $table->time('max_time1')->nullable();
            $table->integer('max_tide2')->nullable();
            $table->time('max_time2')->nullable();
            $table->integer('max_tide3')->nullable();
            $table->time('max_time3')->nullable();
            $table->integer('max_tide4')->nullable();
            $table->time('max_time4')->nullable();

            $table->integer('min_tide1')->nullable();
            $table->time('min_time1')->nullable();
            $table->integer('min_tide2')->nullable();
            $table->time('min_time2')->nullable();
            $table->integer('min_tide3')->nullable();
            $table->time('min_time3')->nullable();
            $table->integer('min_tide4')->nullable();
            $table->time('min_time4')->nullable();

            $table->datetime('created_at');
            $table->datetime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tides');
    }
}
