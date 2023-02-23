<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMaDetailTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('ma_detail', function(Blueprint $table)
		{
			$table->foreign('ma_id', 'fk_ma')->references('id')->on('ma')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('ma_detail', function(Blueprint $table)
		{
			$table->dropForeign('fk_ma');
		});
	}

}
