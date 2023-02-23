<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultsAndNullableToAllTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('login_attempts', function(Blueprint $table)
        {
            $table->dropForeign('fk_login_user');
        });
        Schema::table('recovery_attempts', function(Blueprint $table)
        {
            $table->dropForeign('fk_recovery_user');
        });

        Schema::table('beobachtung', function (Blueprint $table) {
            $table->integer('bewertung')->default(1)->change();
            $table->string('kommentar', 1023)->nullable()->change();
        });
        Schema::table('block', function (Blueprint $table) {
            $table->integer('blocknummer')->nullable()->change();
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->string('kursnummer', 256)->nullable()->change();
        });
        Schema::dropIfExists('login_attempts');
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->integer('killer')->default(0)->change();
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->integer('killer')->default(0)->change();
        });
        Schema::dropIfExists('recovery_attempts');
        Schema::table('tn', function (Blueprint $table) {
            $table->string('abteilung')->nullable()->change();
            $table->string('bild_url')->nullable()->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->dropColumn('kurs_id');
            $table->string('abteilung', 256)->nullable()->change();
            $table->dropColumn('salt');
            $table->string('bild_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beobachtung', function (Blueprint $table) {
            $table->integer('bewertung')->change();
            $table->string('kommentar', 1023)->change();
        });
        Schema::table('block', function (Blueprint $table) {
            $table->integer('blocknummer')->nullable(false)->change();
        });
        Schema::table('kurs', function (Blueprint $table) {
            $table->string('kursnummer', 256)->nullable(false)->change();
        });
        Schema::create('login_attempts', function(Blueprint $table)
        {
            $table->integer('user_id')->index('fk_login_user');
            $table->string('time', 30);
            $table->timestamps();
        });
        Schema::table('ma_detail', function (Blueprint $table) {
            $table->integer('killer')->change();
        });
        Schema::table('ma', function (Blueprint $table) {
            $table->integer('killer')->change();
        });
        Schema::create('recovery_attempts', function(Blueprint $table)
        {
            $table->integer('user_id')->index('fk_recovery_user');
            $table->string('time', 30);
            $table->string('key', 128);
            $table->timestamps();
        });
        Schema::table('tn', function (Blueprint $table) {
            $table->string('abteilung')->nullable(false)->change();
            $table->string('bild_url')->nullable(false)->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
            $table->integer('kurs_id');
            $table->string('abteilung', 256)->nullable(false)->change();
            $table->char('salt', 128);
            $table->string('bild_url')->nullable(false)->change();
        });

        Schema::table('login_attempts', function(Blueprint $table)
        {
            $table->foreign('user_id', 'fk_login_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('recovery_attempts', function(Blueprint $table)
        {
            $table->foreign('user_id', 'fk_recovery_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }
}
