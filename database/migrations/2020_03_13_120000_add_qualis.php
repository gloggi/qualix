<?php

use App\Models\Observation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddQualis extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('quali_datas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->integer('course_id');
            $table->timestamps();
            $table->foreign('course_id', 'fk_quali_datas_course_id')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::create('qualis', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quali_data_id');
            $table->integer('participant_id');
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->foreign('quali_data_id', 'fk_qualis_quali_data_id')->references('id')->on('quali_datas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_qualis_participant_id')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_qualis_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->unique(['quali_data_id', 'participant_id']);
        });

        Schema::create('quali_observations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('observation_id');
            $table->integer('quali_id');
            $table->integer('order');
            $table->foreign('observation_id', 'fk_quali_observations_observation_id')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('quali_id', 'fk_quali_observations_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::create('quali_notes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quali_id');
            $table->integer('order');
            $table->string('notes', 2047);
            $table->timestamps();
            $table->foreign('quali_id', 'fk_quali_notes_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::create('quali_requirements', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quali_id');
            $table->integer('requirement_id');
            $table->integer('order');
            $table->boolean('passed')->nullable();
            $table->timestamps();
            $table->foreign('quali_id', 'fk_quali_requirements_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('requirement_id', 'fk_quali_requirements_requirement_id')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->unique(['quali_id', 'requirement_id']);
        });

        Schema::create('quali_requirement_observations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('observation_id');
            $table->integer('quali_requirement_id');
            $table->integer('order');
            $table->foreign('observation_id', 'fk_quali_requirement_observations_observation_id')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('quali_requirement_id', 'fk_quali_requirement_observations_quali_requirement_id')->references('id')->on('quali_requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::create('quali_requirement_notes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quali_requirement_id');
            $table->integer('order');
            $table->timestamps();
            $table->string('notes', 2047);
            $table->foreign('quali_requirement_id', 'fk_quali_requirement_notes_quali_requirement_id')->references('id')->on('quali_requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('quali_requirement_notes');
        Schema::drop('quali_requirement_observations');
        Schema::drop('quali_requirements');
        Schema::drop('quali_notes');
        Schema::drop('quali_observations');
        Schema::drop('qualis');
        Schema::drop('quali_datas');
    }
}
