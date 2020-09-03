<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLeiterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leiter', function(Blueprint $table)
		{
			$table->foreign('kurs_id', 'fk_kurs_user')->references('id')->on('kurs')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'fk_user_kurs')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leiter', function(Blueprint $table)
		{
			$table->dropForeign('fk_kurs_user');
			$table->dropForeign('fk_user_kurs');
		});
	}

}
