<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBeobachtungTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('beobachtung', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('tn_id')->index('fk_tn_beobachtung');
			$table->integer('block_id')->index('fk_block_beobachtung');
			$table->integer('bewertung');
			$table->string('kommentar', 1023);
			$table->integer('user_id')->index('fk_user_beobachtung');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('beobachtung');
	}

}
