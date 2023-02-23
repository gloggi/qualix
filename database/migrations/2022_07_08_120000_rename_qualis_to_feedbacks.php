<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameQualisToFeedbacks extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::rename('quali_datas', 'feedback_datas');
        Schema::rename('qualis', 'feedbacks');
        Schema::rename('quali_observations_participants', 'feedback_observations_participants');
        Schema::rename('quali_content_nodes', 'feedback_content_nodes');
        Schema::rename('quali_requirements', 'feedback_requirements');
        Schema::rename('qualis_users', 'feedbacks_users');

        Schema::table('feedback_datas', function (Blueprint $table) {
            $table->dropForeign('fk_quali_datas_course_id');
            $table->foreign('course_id', 'fk_feedback_datas_course_id')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign('fk_qualis_participant_id');
            $table->dropForeign('fk_qualis_quali_data_id');
            $table->dropUnique('qualis_quali_data_id_participant_id_unique');
            $table->renameColumn('quali_data_id', 'feedback_data_id');
            $table->unique(['feedback_data_id', 'participant_id']);
            $table->foreign('feedback_data_id', 'fk_feedbacks_feedback_data_id')->references('id')->on('feedback_datas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_feedbacks_participant_id')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_observations_participants', function (Blueprint $table) {
            $table->dropForeign('fk_quali_observations_participants_quali_id');
            $table->dropForeign('fk_quali_observations_participants_participant_observation_id');
            $table->renameColumn('quali_id', 'feedback_id');
            $table->foreign('participant_observation_id', 'fk_feedback_observations_participants_participant_observation_id')->references('id')->on('observations_participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('feedback_id', 'fk_feedback_observations_participants_feedback_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_content_nodes', function (Blueprint $table) {
            $table->dropForeign('fk_quali_content_nodes_quali_id');
            $table->renameColumn('quali_id', 'feedback_id');
            $table->foreign('feedback_id', 'fk_feedback_content_nodes_feedback_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_requirements', function (Blueprint $table) {
            $table->dropForeign('fk_quali_requirements_requirement_id');
            $table->dropForeign('fk_quali_requirements_quali_id');
            $table->dropUnique('quali_requirements_quali_id_requirement_id_unique');
            $table->renameColumn('quali_id', 'feedback_id');
            $table->unique(['feedback_id', 'requirement_id']);
            $table->foreign('feedback_id', 'fk_feedback_requirements_feedback_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('requirement_id', 'fk_feedback_requirements_requirement_id')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedbacks_users', function (Blueprint $table) {
            $table->dropForeign('fk_qualis_users_user_id');
            $table->dropForeign('fk_qualis_users_quali_id');
            $table->renameColumn('quali_id', 'feedback_id');
            $table->foreign('feedback_id', 'fk_feedbacks_users_feedback_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_feedbacks_users_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('feedbacks_users', function (Blueprint $table) {
            $table->dropForeign('fk_feedbacks_users_feedback_id');
            $table->dropForeign('fk_feedbacks_users_user_id');
            $table->renameColumn('feedback_id', 'quali_id');
            $table->foreign('quali_id', 'fk_qualis_users_user_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_qualis_users_quali_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_requirements', function (Blueprint $table) {
            $table->dropForeign('fk_feedback_requirements_requirement_id');
            $table->dropForeign('fk_feedback_requirements_feedback_id');
            $table->dropUnique(['feedback_id', 'requirement_id']);
            $table->renameColumn('feedback_id', 'quali_id');
            $table->unique(['quali_id', 'requirement_id'], 'quali_requirements_quali_id_requirement_id_unique');
            $table->foreign('quali_id', 'fk_quali_requirements_quali_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('requirement_id', 'fk_quali_requirements_requirement_id')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_content_nodes', function (Blueprint $table) {
            $table->dropForeign('fk_feedback_content_nodes_feedback_id');
            $table->renameColumn('feedback_id', 'quali_id');
            $table->foreign('quali_id', 'fk_quali_content_nodes_quali_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_observations_participants', function (Blueprint $table) {
            $table->dropForeign('fk_feedback_observations_participants_feedback_id');
            $table->dropForeign('fk_feedback_observations_participants_participant_observation_id');
            $table->renameColumn('feedback_id', 'quali_id');
            $table->foreign('participant_observation_id', 'fk_quali_observations_participants_participant_observation_id')->references('id')->on('observations_participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('quali_id', 'fk_quali_observations_participants_quali_id')->references('id')->on('feedbacks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign('fk_feedbacks_participant_id');
            $table->dropForeign('fk_feedbacks_feedback_data_id');
            $table->dropUnique(['feedback_data_id', 'participant_id']);
            $table->renameColumn('feedback_data_id', 'quali_data_id');
            $table->unique(['quali_data_id', 'participant_id'], 'qualis_quali_data_id_participant_id_unique');
            $table->foreign('quali_data_id', 'fk_qualis_quali_data_id')->references('id')->on('feedback_datas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_qualis_participant_id')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feedback_datas', function (Blueprint $table) {
            $table->dropForeign('fk_feedback_datas_course_id');
            $table->foreign('course_id', 'fk_quali_datas_course_id')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('feedback_datas', 'quali_datas');
        Schema::rename('feedbacks', 'qualis');
        Schema::rename('feedback_observations_participants', 'quali_observations_participants');
        Schema::rename('feedback_content_nodes', 'quali_content_nodes');
        Schema::rename('feedback_requirements', 'quali_requirements');
        Schema::rename('feedbacks_users', 'qualis_users');
    }
}
