<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecoveryAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('recovery_attempts', function(Blueprint $table)
		{
			$table->integer('user_id')->index('fk_recovery_user');
			$table->string('time', 30);
			$table->string('key', 128);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('recovery_attempts');
	}

}
