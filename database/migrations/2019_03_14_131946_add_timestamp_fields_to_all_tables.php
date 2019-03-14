<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampFieldsToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beobachtung', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('block', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('einladung', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('leiter', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('qk', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('recovery_attempts', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('tn', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beobachtung', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('block', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('einladung', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('leiter', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('qk', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('recovery_attempts', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('tn', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
