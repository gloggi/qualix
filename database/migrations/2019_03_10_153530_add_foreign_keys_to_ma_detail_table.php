<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMaDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ma_detail', function(Blueprint $table)
		{
			$table->foreign('ma_id', 'fk_ma')->references('id')->on('ma')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ma_detail', function(Blueprint $table)
		{
			$table->dropForeign('fk_ma');
		});
	}

}
