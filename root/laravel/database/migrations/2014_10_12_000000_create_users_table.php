<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('user_id', 100)->unique();
            $table->string('hashed_id', 40)->unique();
            $table->string('login_id', 100)->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('sei', 32)->nullable();
            $table->string('mei', 32)->nullable();
            $table->string('sei_kana', 64)->nullable();
            $table->string('mei_kana', 64)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('tel', 20)->nullable();

            $table->string('icon_filepath')->nullable();
            $table->datetime('last_logined_at')->nullable();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->datetime('deleted_at')->nullable();
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
