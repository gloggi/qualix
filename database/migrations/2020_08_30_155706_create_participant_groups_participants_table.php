<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantGroupsParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_groups_participants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('participant_group_id')->nullable(false);
            $table->integer('participant_id')->nullable(false);
            $table->timestamps();
        });

        Schema::table('participant_groups_participants', function(Blueprint $table)
        {
            $table->foreign('participant_group_id', 'fk_participant_group_participant_groups_participants')->references('id')->on('participant_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_participants_groups_participants')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant_groups_participants', function(Blueprint $table)
        {
            $table->dropForeign('fk_participants_groups_participants');
            $table->dropForeign('fk_participant_group_participant_groups_participants');
        });
        Schema::dropIfExists('participant_groups_participants');
    }
}
