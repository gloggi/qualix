<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationOrderUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_order_users', function (Blueprint $table) {
            $table->integer('observation_order_id')->nullable(false);
            $table->integer('user_id')->nullable(false);
        });

        Schema::table('observation_order_users', function (Blueprint $table) {
            $table->foreign('observation_order_id', 'fk_order_observation_order_user')->references('id')->on('observation_orders')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_observation_order_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_order_users', function(Blueprint $table)
        {
            $table->dropForeign('fk_user_observation_order_user');
            $table->dropForeign('fk_order_observation_order_user');
        });
        Schema::dropIfExists('observation_order_users');
    }
}
