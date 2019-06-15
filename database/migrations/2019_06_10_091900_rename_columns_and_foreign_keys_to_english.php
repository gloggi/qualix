<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsAndForeignKeysToEnglish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('kurs', 'courses');
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('kursnummer', 'course_number');
        });

        Schema::rename('tn', 'participants');
        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('pfadiname', 'scout_name');
            $table->renameColumn('abteilung', 'group');
            $table->renameColumn('kurs_id', 'course_id');
            $table->renameColumn('bild_url', 'image_url');

            $table->dropForeign('fk_kurs_tn');
            $table->foreign('course_id', 'fk_courses_participants')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('ma', 'requirements');
        Schema::table('requirements', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
            $table->renameColumn('anforderung', 'content');
            $table->renameColumn('killer', 'mandatory');

            $table->dropForeign('fk_kurs_ma');
            $table->foreign('course_id', 'fk_courses_requirements')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('ma_detail', 'requirement_details');
        Schema::table('requirement_details', function (Blueprint $table) {
            $table->renameColumn('ma_id', 'requirement_id');
            $table->renameColumn('ma_definition', 'content');
            $table->renameColumn('killer', 'mandatory');

            $table->dropForeign('fk_ma');
            $table->foreign('requirement_id', 'fk_requirements')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('qk', 'categories');
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('quali_kategorie', 'name');
            $table->renameColumn('kurs_id', 'course_id');

            $table->dropForeign('fk_kurs_qk');
            $table->foreign('course_id', 'fk_courses_categories')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('block', 'blocks');
        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
            $table->renameColumn('blockname', 'name');
            $table->renameColumn('datum', 'block_date');
            $table->renameColumn('tagesnummer', 'day_number');
            $table->renameColumn('blocknummer', 'block_number');

            $table->dropForeign('fk_kurs_block');
            $table->foreign('course_id', 'fk_courses_blocks')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('beobachtung', 'observations');
        Schema::table('observations', function (Blueprint $table) {
            $table->renameColumn('tn_id', 'participant_id');
            $table->renameColumn('bewertung', 'impression');
            $table->renameColumn('kommentar', 'content');

            $table->dropForeign('fk_block_beobachtung');
            $table->dropForeign('fk_tn_beobachtung');
            $table->dropForeign('fk_user_beobachtung');
            $table->foreign('block_id', 'fk_blocks_observations')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('participant_id', 'fk_participants_observations')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_users_observations')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('beobachtung_ma', 'observations_requirements');
        Schema::table('observations_requirements', function (Blueprint $table) {
            $table->renameColumn('beobachtung_id', 'observation_id');
            $table->renameColumn('ma_id', 'requirement_id');

            $table->dropForeign('fk_beobachtung_ma');
            $table->dropForeign('fk_ma_beobachtung');
            $table->foreign('observation_id', 'fk_observations_requirements')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('requirement_id', 'fk_requirements_observations')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('beobachtung_qk', 'observations_categories');
        Schema::table('observations_categories', function (Blueprint $table) {
            $table->renameColumn('beobachtung_id', 'observation_id');
            $table->renameColumn('qk_id', 'category_id');

            $table->dropForeign('fk_beobachtung_qk');
            $table->dropForeign('fk_qk_beobachtung');
            $table->foreign('observation_id', 'fk_observations_categories')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('category_id', 'fk_categories_observations')->references('id')->on('categories')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('block_ma', 'blocks_requirements');
        Schema::table('blocks_requirements', function (Blueprint $table) {
            $table->renameColumn('ma_id', 'requirement_id');

            $table->dropForeign('fk_block_ma');
            $table->dropForeign('fk_ma_block');
            $table->foreign('block_id', 'fk_blocks_requirements')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('requirement_id', 'fk_requirements_blocks')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('einladung', 'invitations');
        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');

            $table->dropForeign('fk_kurs_einladung');
            $table->foreign('course_id', 'fk_courses_invitations')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::rename('leiter', 'trainers');
        Schema::table('trainers', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');

            $table->dropForeign('fk_kurs_user');
            $table->dropForeign('fk_user_kurs');
            $table->foreign('course_id', 'fk_courses_users')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_users_courses')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('abteilung', 'group');
            $table->renameColumn('bild_url', 'image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('image_url', 'bild_url');
            $table->renameColumn('group', 'abteilung');
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->renameColumn('course_id', 'kurs_id');

            $table->dropForeign('fk_courses_users');
            $table->dropForeign('fk_users_courses');
            $table->foreign('kurs_id', 'fk_kurs_user')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_kurs')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('trainers', 'leiter');

        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('course_id', 'kurs_id');

            $table->dropForeign('fk_courses_invitations');
            $table->foreign('kurs_id', 'fk_kurs_einladung')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('invitations', 'einladung');

        Schema::table('blocks_requirements', function (Blueprint $table) {
            $table->renameColumn('requirement_id', 'ma_id');

            $table->dropForeign('fk_blocks_requirements');
            $table->dropForeign('fk_requirements_blocks');
            $table->foreign('block_id', 'fk_block_ma')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ma_id', 'fk_ma_block')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('blocks_requirements', 'block_ma');

        Schema::table('observations_categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'qk_id');
            $table->renameColumn('observation_id', 'beobachtung_id');

            $table->dropForeign('fk_observations_categories');
            $table->dropForeign('fk_categories_observations');
            $table->foreign('beobachtung_id', 'fk_beobachtung_qk')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('qk_id', 'fk_qk_beobachtung')->references('id')->on('categories')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('observations_categories', 'beobachtung_qk');

        Schema::table('observations_requirements', function (Blueprint $table) {
            $table->renameColumn('requirement_id', 'ma_id');
            $table->renameColumn('observation_id', 'beobachtung_id');

            $table->dropForeign('fk_observations_requirements');
            $table->dropForeign('fk_requirements_observations');
            $table->foreign('beobachtung_id', 'fk_beobachtung_ma')->references('id')->on('observations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ma_id', 'fk_ma_beobachtung')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('observations_requirements', 'beobachtung_ma');

        Schema::table('observations', function (Blueprint $table) {
            $table->renameColumn('content', 'kommentar');
            $table->renameColumn('impression', 'bewertung');
            $table->renameColumn('participant_id', 'tn_id');

            $table->dropForeign('fk_blocks_observations');
            $table->dropForeign('fk_participants_observations');
            $table->dropForeign('fk_users_observations');
            $table->foreign('block_id', 'fk_block_beobachtung')->references('id')->on('blocks')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('tn_id', 'fk_tn_beobachtung')->references('id')->on('participants')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_beobachtung')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('observations', 'beobachtung');

        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('block_number', 'blocknummer');
            $table->renameColumn('day_number', 'tagesnummer');
            $table->renameColumn('block_date', 'datum');
            $table->renameColumn('name', 'blockname');
            $table->renameColumn('course_id', 'kurs_id');

            $table->dropForeign('fk_courses_blocks');
            $table->foreign('kurs_id', 'fk_kurs_block')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('blocks', 'block');

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('course_id', 'kurs_id');
            $table->renameColumn('name', 'quali_kategorie');

            $table->dropForeign('fk_courses_categories');
            $table->foreign('kurs_id', 'fk_kurs_qk')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('categories', 'qk');

        Schema::table('requirement_details', function (Blueprint $table) {
            $table->renameColumn('mandatory', 'killer');
            $table->renameColumn('content', 'ma_definition');
            $table->renameColumn('requirement_id', 'ma_id');

            $table->dropForeign('fk_requirements');
            $table->foreign('ma_id', 'fk_ma')->references('id')->on('requirements')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('requirement_details', 'ma_detail');

        Schema::table('requirements', function (Blueprint $table) {
            $table->renameColumn('mandatory', 'killer');
            $table->renameColumn('content', 'anforderung');
            $table->renameColumn('course_id', 'kurs_id');

            $table->dropForeign('fk_courses_requirements');
            $table->foreign('kurs_id', 'fk_kurs_ma')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('requirements', 'ma');

        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('image_url', 'bild_url');
            $table->renameColumn('course_id', 'kurs_id');
            $table->renameColumn('group', 'abteilung');
            $table->renameColumn('scout_name', 'pfadiname');

            $table->dropForeign('fk_courses_participants');
            $table->foreign('kurs_id', 'fk_kurs_tn')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::rename('participants', 'tn');

        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('course_number', 'kursnummer');
        });
        Schema::rename('courses', 'kurs');
    }
}
