<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdaptBlockTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('block', function(Blueprint $table)
        {
            $table->integer('tagesnummer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block', function(Blueprint $table)
        {
            $table->integer('tagesnummer')->nullable(false)->change();
        });
    }
}
