<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ma', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('kurs_id')->index('fk_kurs_ma');
			$table->string('anforderung');
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
		Schema::drop('ma');
	}

}
