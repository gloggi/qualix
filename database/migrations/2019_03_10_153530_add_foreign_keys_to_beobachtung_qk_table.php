<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBeobachtungQkTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('beobachtung_qk', function(Blueprint $table)
		{
			$table->foreign('beobachtung_id', 'fk_beobachtung_qk')->references('id')->on('beobachtung')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('qk_id', 'fk_qk_beobachtung')->references('id')->on('qk')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('beobachtung_qk', function(Blueprint $table)
		{
			$table->dropForeign('fk_beobachtung_qk');
			$table->dropForeign('fk_qk_beobachtung');
		});
	}

}
