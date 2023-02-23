<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCommentToFeedbackRequirement extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feedback_requirements', function (Blueprint $table) {
            $table->string('comment', 16000)->default('')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_requirements', function (Blueprint $table) {
            $table->removeColumn('comment');
        });
    }
}
