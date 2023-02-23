<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockMaTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('block_ma', function(Blueprint $table)
		{
			$table->integer('ma_id');
			$table->integer('block_id')->index('fk_block_ma');
			$table->primary(['ma_id','block_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('block_ma');
	}

}
