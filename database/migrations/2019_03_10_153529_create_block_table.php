<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('block', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('tagesnummer');
			$table->string('blockname', 256);
			$table->date('datum');
			$table->integer('kurs_id')->index('fk_kurs_block');
			$table->integer('blocknummer');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('block');
	}

}
