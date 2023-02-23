<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastAccessedToLeiter extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leiter', function (Blueprint $table) {
            $table->timestamp('last_accessed')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leiter', function (Blueprint $table) {
            $table->dropColumn('last_accessed');
        });
    }
}
