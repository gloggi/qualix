<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationAssignmentUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('observation_assignment_users', function (Blueprint $table) {
            $table->integer('observation_assignment_id')->nullable(false);
            $table->integer('user_id')->nullable(false);
        });

        Schema::table('observation_assignment_users', function (Blueprint $table) {
            $table->foreign('observation_assignment_id', 'fk_order_observation_assignment_user')->references('id')->on('observation_assignments')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_observation_assignment_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observation_assignment_users', function(Blueprint $table)
        {
            $table->dropForeign('fk_user_observation_assignment_user');
            $table->dropForeign('fk_order_observation_assignment_user');
        });
        Schema::dropIfExists('observation_assignment_users');
    }
}
