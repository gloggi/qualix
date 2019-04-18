<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastUsedBlockDateToLeiter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leiter', function(Blueprint $table)
        {
            $table->date('last_used_block_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leiter', function(Blueprint $table)
        {
            $table->dropColumn('last_used_block_date');
        });
    }
}
