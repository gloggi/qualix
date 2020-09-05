<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationOrderparticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_order_participants', function (Blueprint $table) {
            $table->integer('order_id')->nullable(false);
            $table->integer('participant_id')->nullable(false);
        });

        Schema::table('observation_order_participants', function (Blueprint $table) {
            $table->foreign('order_id', 'fk_order_observation_order_participant')->references('id')->on('observation_orders')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_participant_observation_order_participant')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_order_participants', function(Blueprint $table)
        {
            $table->dropForeign('fk_participant_observation_order_participant');
            $table->dropForeign('fk_order_observation_order_participant');
        });
        Schema::dropIfExists('observation_order_participants');
    }
}
