<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBeobachtungMaTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('beobachtung_ma', function(Blueprint $table)
		{
			$table->foreign('beobachtung_id', 'fk_beobachtung_ma')->references('id')->on('beobachtung')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ma_id', 'fk_ma_beobachtung')->references('id')->on('ma')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('beobachtung_ma', function(Blueprint $table)
		{
			$table->dropForeign('fk_beobachtung_ma');
			$table->dropForeign('fk_ma_beobachtung');
		});
	}

}
