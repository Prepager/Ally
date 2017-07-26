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
            $table->integer('team_id')->default(0);
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('email_verified')->default(0);
            $table->string('email_token')->nullable();
            $table->string('password');
            $table->string('country');
            $table->string('avatar')->nullable();
            $table->string('suspended_reason')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('suspended_to')->nullable();
            $table->rememberToken();
            $table->timestamps();
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
