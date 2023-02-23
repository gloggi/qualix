<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoginAttemptsTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('login_attempts', function(Blueprint $table)
		{
			$table->integer('user_id')->index('fk_login_user');
			$table->string('time', 30);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('login_attempts');
	}

}
