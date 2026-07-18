<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeObservationUserRelationToManyToMany extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('observations_users', function(Blueprint $table)
        {
            $table->integer('observation_id');
            $table->integer('user_id');
            $table->unsignedInteger('order')->default(0);
            $table->foreign('observation_id', 'fk_observations_users_observation_id')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_observations_users_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->primary(['observation_id','user_id']);
        });

        DB::table('observations_users')->insertUsing(['observation_id', 'user_id'], DB::table('observations')->select(['id', 'user_id'])->whereNotNull('user_id'));

        Schema::table('observations', function (Blueprint $table) {
            $table->dropForeign('fk_users_observations');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->foreign('user_id', 'fk_users_observations')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

        // UPDATE observations o JOIN observations_users ou ON o.id=ou.observation_id SET o.user_id=ou.user_id
        DB::table('observations as o')
            ->join('observations_users as ou', 'o.id', '=', 'ou.observation_id')
            ->update(['o.user_id' => DB::raw('ou.user_id')]);

        Schema::drop('observations_users');
    }
}
