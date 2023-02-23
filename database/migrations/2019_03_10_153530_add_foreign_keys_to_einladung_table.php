<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEinladungTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('einladung', function(Blueprint $table)
		{
			$table->foreign('kurs_id', 'fk_kurs_einladung')->references('id')->on('kurs')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('einladung', function(Blueprint $table)
		{
			$table->dropForeign('fk_kurs_einladung');
		});
	}

}
