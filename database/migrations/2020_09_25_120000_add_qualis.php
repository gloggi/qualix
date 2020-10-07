<?php

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


        Schema::table('observations_participants', function (Blueprint $table) {
            $table->unique(['observation_id','participant_id']);
            $table->dropPrimary();
        });

        Schema::table('observations_participants', function (Blueprint $table) {
            $table->integer('id', true);
        });

        Schema::create('quali_observations_participants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('participant_observation_id');
            $table->integer('quali_id');
            $table->integer('order');
            $table->foreign('participant_observation_id', 'fk_quali_observations_participants_participant_observation_id')->references('id')->on('observations_participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('quali_id', 'fk_quali_observations_participants_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::create('quali_content_nodes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quali_id');
            $table->integer('order');
            $table->json('json');
            $table->timestamps();
            $table->foreign('quali_id', 'fk_quali_content_nodes_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('quali_requirements');
        Schema::drop('quali_content_nodes');
        Schema::drop('quali_participant_observations');
        Schema::table('observations_participants', function (Blueprint $table) {
            $table->dropPrimary();
        });
        Schema::table('observations_participants', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropUnique('observations_participants_observation_id_participant_id_unique');
            $table->primary(['observation_id','participant_id']);
        });
        Schema::drop('qualis');
        Schema::drop('quali_datas');
    }
}
