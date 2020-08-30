<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_groups', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id');
            $table->string('group_name');
            $table->timestamps();
        });
        Schema::table('participant_groups', function(Blueprint $table)
        {
            $table->foreign('course_id', 'fk_course_participant_group')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant_groups', function(Blueprint $table)
        {
            $table->dropForeign('fk_course_participant_group');
        });
        Schema::dropIfExists('participant_groups');
    }
}
