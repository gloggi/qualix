<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBeobachtungMaTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('beobachtung_ma', function(Blueprint $table)
		{
			$table->integer('beobachtung_id');
			$table->integer('ma_id')->index('fk_ma_beobachtung');
			$table->primary(['beobachtung_id','ma_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('beobachtung_ma');
	}

}
