<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBlockMaTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('block_ma', function(Blueprint $table)
		{
			$table->foreign('block_id', 'fk_block_ma')->references('id')->on('block')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ma_id', 'fk_ma_block')->references('id')->on('ma')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('block_ma', function(Blueprint $table)
		{
			$table->dropForeign('fk_block_ma');
			$table->dropForeign('fk_ma_block');
		});
	}

}
