<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBeobachtungTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beobachtung', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('tn_id')->index('fk_tn_beobachtung');
			$table->integer('block_id')->index('fk_block_beobachtung');
			$table->integer('bewertung');
			$table->string('kommentar', 1023);
			$table->integer('user_id')->index('fk_user_beobachtung');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('beobachtung');
	}

}
