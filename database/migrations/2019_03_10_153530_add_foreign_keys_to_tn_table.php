<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTnTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tn', function(Blueprint $table)
		{
			$table->foreign('kurs_id', 'fk_kurs_tn')->references('id')->on('kurs')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tn', function(Blueprint $table)
		{
			$table->dropForeign('fk_kurs_tn');
		});
	}

}
