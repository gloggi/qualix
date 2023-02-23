<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastUsedBlockDateToLeiter extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leiter', function(Blueprint $table)
        {
            $table->date('last_used_block_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leiter', function(Blueprint $table)
        {
            $table->dropColumn('last_used_block_date');
        });
    }
}
