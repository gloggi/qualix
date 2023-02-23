<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLoginAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('login_attempts', function(Blueprint $table)
		{
			$table->foreign('user_id', 'fk_login_user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('login_attempts', function(Blueprint $table)
		{
			$table->dropForeign('fk_login_user');
		});
	}

}
