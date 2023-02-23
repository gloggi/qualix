<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecoveryAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
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
	 */
	public function down(): void
	{
		Schema::drop('recovery_attempts');
	}

}
