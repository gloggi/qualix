<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddObservationThresholds extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('observation_count_red_threshold')->default(5);
            $table->integer('observation_count_green_threshold')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('observation_count_red_threshold');
            $table->dropColumn('observation_count_green_threshold');
        });
    }
}
