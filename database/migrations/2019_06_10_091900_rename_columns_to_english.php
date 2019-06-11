<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnsToEnglish extends Migration
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
        });

        Schema::rename('ma', 'requirements');
        Schema::table('requirements', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
            $table->renameColumn('anforderung', 'content');
            $table->renameColumn('killer', 'mandatory');
        });

        Schema::rename('ma_detail', 'requirement_details');
        Schema::table('requirement_details', function (Blueprint $table) {
            $table->renameColumn('ma_id', 'requirement_id');
            $table->renameColumn('ma_definition', 'content');
            $table->renameColumn('killer', 'mandatory');
        });

        Schema::rename('qk', 'categories');
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('quali_kategorie', 'name');
            $table->renameColumn('kurs_id', 'course_id');
        });

        Schema::rename('beobachtung', 'observations');
        Schema::table('observations', function (Blueprint $table) {
            $table->renameColumn('tn_id', 'participant_id');
            $table->renameColumn('bewertung', 'impression');
            $table->renameColumn('kommentar', 'content');
        });

        Schema::rename('beobachtung_ma', 'observations_requirements');
        Schema::table('observations_requirements', function (Blueprint $table) {
            $table->renameColumn('beobachtung_id', 'observation_id');
            $table->renameColumn('ma_id', 'requirement_id');
        });

        Schema::rename('beobachtung_qk', 'observations_categories');
        Schema::table('observations_categories', function (Blueprint $table) {
            $table->renameColumn('beobachtung_id', 'observation_id');
            $table->renameColumn('qk_id', 'category_id');
        });

        Schema::rename('block', 'blocks');
        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
            $table->renameColumn('blockname', 'name');
            $table->renameColumn('datum', 'block_date');
            $table->renameColumn('tagesnummer', 'day_number');
            $table->renameColumn('blocknummer', 'block_number');
        });

        Schema::rename('block_ma', 'blocks_requirements');
        Schema::table('blocks_requirements', function (Blueprint $table) {
            $table->renameColumn('ma_id', 'requirement_id');
        });

        Schema::rename('einladung', 'invitations');
        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
        });

        Schema::rename('leiter', 'trainers');
        Schema::table('trainers', function (Blueprint $table) {
            $table->renameColumn('kurs_id', 'course_id');
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
        });
        Schema::rename('trainers', 'leiter');

        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('course_id', 'kurs_id');
        });
        Schema::rename('invitations', 'einladung');

        Schema::table('blocks_requirements', function (Blueprint $table) {
            $table->renameColumn('requirement_id', 'ma_id');
        });
        Schema::rename('blocks_requirements', 'block_ma');

        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('block_number', 'blocknummer');
            $table->renameColumn('day_number', 'tagesnummer');
            $table->renameColumn('block_date', 'datum');
            $table->renameColumn('name', 'blockname');
            $table->renameColumn('course_id', 'kurs_id');
        });
        Schema::rename('blocks', 'block');

        Schema::table('observations_categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'qk_id');
            $table->renameColumn('observation_id', 'beobachtung_id');
        });
        Schema::rename('observations_categories', 'beobachtung_qk');

        Schema::table('observations_requirements', function (Blueprint $table) {
            $table->renameColumn('requirement_id', 'ma_id');
            $table->renameColumn('observation_id', 'beobachtung_id');
        });
        Schema::rename('observations_requirements', 'beobachtung_ma');

        Schema::table('observations', function (Blueprint $table) {
            $table->renameColumn('content', 'kommentar');
            $table->renameColumn('impression', 'bewertung');
            $table->renameColumn('participant_id', 'tn_id');
        });
        Schema::rename('observations', 'beobachtung');

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('course_id', 'kurs_id');
            $table->renameColumn('name', 'quali_kategorie');
        });
        Schema::rename('categories', 'qk');

        Schema::table('requirement_details', function (Blueprint $table) {
            $table->renameColumn('mandatory', 'killer');
            $table->renameColumn('content', 'ma_definition');
            $table->renameColumn('requirement_id', 'ma_id');
        });
        Schema::rename('requirement_details', 'ma_detail');

        Schema::table('requirements', function (Blueprint $table) {
            $table->renameColumn('mandatory', 'killer');
            $table->renameColumn('content', 'anforderung');
            $table->renameColumn('course_id', 'kurs_id');
        });
        Schema::rename('requirements', 'ma');

        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('image_url', 'bild_url');
            $table->renameColumn('course_id', 'kurs_id');
            $table->renameColumn('group', 'abteilung');
            $table->renameColumn('scout_name', 'pfadiname');
        });
        Schema::rename('participants', 'tn');

        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('course_number', 'kursnummer');
        });
        Schema::rename('courses', 'kurs');
    }
}
