const COLLABORATION_KEY_LENGTH = 32;<?php

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
            $table->string('collaborationKey', COLLABORATION_KEY_LENGTH);
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
