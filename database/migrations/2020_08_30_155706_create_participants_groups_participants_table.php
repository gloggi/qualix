<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsGroupsParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants_groups_participants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('participant_group_id');
            $table->integer('participant_id');
            $table->timestamps();
        });

        Schema::table('participants_groups_participants', function(Blueprint $table)
        {
            $table->foreign('participant_group_id', 'fk_participant_group_participants_groups_participants')->references('id')->on('participants_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::table('participants_groups_participants', function(Blueprint $table)
        {
            $table->dropForeign('fk_participants_groups_participants');
            $table->dropForeign('fk_participant_group_participants_groups_participants');
        });
        Schema::dropIfExists('participants_groups_participants');
    }
}
