<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollaborationKeyToQuali extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qualis', function (Blueprint $table) {
            $table->string('collaborationKey', 32);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualis', function (Blueprint $table) {
            $table->dropColumn('collaborationKey');
        });
    }
}
