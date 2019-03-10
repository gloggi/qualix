<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ma_detail', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('ma_definition', 512);
			$table->integer('ma_id')->index('fk_ma');
			$table->integer('killer');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ma_detail');
	}

}
