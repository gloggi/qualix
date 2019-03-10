<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRecoveryAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('recovery_attempts', function(Blueprint $table)
		{
			$table->foreign('user_id', 'fk_recovery_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('recovery_attempts', function(Blueprint $table)
		{
			$table->dropForeign('fk_recovery_user');
		});
	}

}
