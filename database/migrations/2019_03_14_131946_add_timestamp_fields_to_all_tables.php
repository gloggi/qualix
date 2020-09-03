<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->timestamps();
        });
        Schema::table('block', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('einladung', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('leiter', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('qk', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('recovery_attempts', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('tn', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->timestamps();
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
            $table->dropTimestamps();
        });
        Schema::table('block', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('einladung', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('leiter', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('qk', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('recovery_attempts', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('tn', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
