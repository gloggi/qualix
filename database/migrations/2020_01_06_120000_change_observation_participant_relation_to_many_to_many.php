<?php

use App\Models\Observation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeObservationParticipantRelationToManyToMany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observations_participants', function(Blueprint $table)
        {
            $table->integer('observation_id');
            $table->integer('participant_id');
            $table->foreign('observation_id', 'fk_observations_participants_observation_id')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_observations_participants_participant_id')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->primary(['observation_id','participant_id']);
        });
        DB::table('observations_participants')->insertUsing(['observation_id', 'participant_id'], DB::table('observations')->select(['id', 'participant_id']));

        Schema::table('observations', function (Blueprint $table) {
            $table->dropForeign('fk_participants_observations');
            $table->dropColumn('participant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->integer('participant_id')->nullable();
        });

        // UPDATE observations o JOIN observations_participants op ON o.id=op.observation_id SET o.participant_id=op.participant_id
        DB::table('observations as o')
            ->join('observations_participants as op', 'o.id', '=', 'op.observation_id')
            ->update(['o.participant_id' => DB::raw('op.participant_id')]);

        Schema::table('observations', function (Blueprint $table) {
            $table->integer('participant_id')->nullable(false)->change();
            $table->foreign('participant_id', 'fk_participants_observations')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::drop('observations_participants');
    }
}
