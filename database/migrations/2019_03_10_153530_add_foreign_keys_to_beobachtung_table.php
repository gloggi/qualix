<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBeobachtungTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('beobachtung', function(Blueprint $table)
		{
			$table->foreign('block_id', 'fk_block_beobachtung')->references('id')->on('block')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('tn_id', 'fk_tn_beobachtung')->references('id')->on('tn')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'fk_user_beobachtung')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('beobachtung', function(Blueprint $table)
		{
			$table->dropForeign('fk_block_beobachtung');
			$table->dropForeign('fk_tn_beobachtung');
			$table->dropForeign('fk_user_beobachtung');
		});
	}

}
