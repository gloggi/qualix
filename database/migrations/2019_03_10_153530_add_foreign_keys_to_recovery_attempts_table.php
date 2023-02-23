<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRecoveryAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('recovery_attempts', function(Blueprint $table)
		{
			$table->foreign('user_id', 'fk_recovery_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('recovery_attempts', function(Blueprint $table)
		{
			$table->dropForeign('fk_recovery_user');
		});
	}

}
