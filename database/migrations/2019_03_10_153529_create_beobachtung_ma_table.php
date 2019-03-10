<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBeobachtungMaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
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
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('beobachtung_ma');
	}

}
