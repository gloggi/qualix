<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeiterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leiter', function(Blueprint $table)
		{
			$table->integer('kurs_id');
			$table->integer('user_id')->index('fk_user_kurs');
			$table->primary(['kurs_id','user_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('leiter');
	}

}
