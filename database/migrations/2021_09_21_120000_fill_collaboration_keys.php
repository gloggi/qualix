<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FillCollaborationKeys extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::table('qualis')->orderBy('id')->each(function ($quali) {
            $quali->collaborationKey = Str::random(32);
            $quali->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
    }
}
