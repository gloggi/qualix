<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_order_details', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false);
            $table->integer('block_id')->nullable(false);
            $table->integer('participant_id')->nullable(false);
        });

        Schema::table('observation_order_details', function (Blueprint $table) {
            $table->foreign('user_id', 'fk_user_observation_order_details')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('block_id', 'fk_block_observation_order_details')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_participants_observation_order_details')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_order_details', function(Blueprint $table)
        {
            $table->dropForeign('fk_user_observation_order_details');
            $table->dropForeign('fk_block_observation_order_details');
            $table->dropForeign('fk_participants_observation_order_details');
        });
        Schema::dropIfExists('observation_order_details');
    }
}
