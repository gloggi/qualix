<?php

use App\Models\Quali;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class FillCollaborationKeys extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Quali::each(function (Quali $quali) {
            $quali->collaborationKey = Str::random(32);
            $quali->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
