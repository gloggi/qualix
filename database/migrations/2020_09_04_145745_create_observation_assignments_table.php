<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_assignments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->nullable(false);
            $table->string('order_name')->nullable(false);
            $table->timestamps();
        });
        Schema::table('observation_assignments', function(Blueprint $table)
        {
            $table->foreign('course_id', 'fk_course_observation_assignment')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_assignments', function(Blueprint $table)
        {
            $table->dropForeign('fk_course_observation_assignment');

        });
        Schema::dropIfExists('observation_assignments');
    }
}
