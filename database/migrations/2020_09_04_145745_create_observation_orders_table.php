<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable(false);
            $table->integer('block_id')->nullable(false);
            $table->integer('participant_id')->nullable(false);
            $table->timestamps();
        });
        Schema::table('observation_orders', function(Blueprint $table)
        {
            $table->foreign('user_id', 'fk_user_observation_orders')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('block_id', 'fk_block_observation_orders')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_participants_observation_orders')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_orders', function(Blueprint $table)
        {
            $table->dropForeign('fk_user_observation_orders');
            $table->dropForeign('fk_block_observation_orders');
            $table->dropForeign('fk_participants_observation_orders');
        });
        Schema::dropIfExists('observation_orders');
    }
}
