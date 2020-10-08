<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationAssignmentBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_assignment_blocks', function (Blueprint $table) {
            $table->integer('observation_assignment_id')->nullable(false);
            $table->integer('block_id')->nullable(false);
        });

        Schema::table('observation_assignment_blocks', function (Blueprint $table) {
            $table->foreign('observation_assignment_id', 'fk_order_observation_assignment_block')->references('id')->on('observation_assignments')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('block_id', 'fk_block_observation_assignment_block')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_assignment_blocks', function(Blueprint $table)
        {
            $table->dropForeign('fk_block_observation_assignment_block');
            $table->dropForeign('fk_order_observation_assignment_block');
        });
        Schema::dropIfExists('observation_assignment_blocks');
    }
}
