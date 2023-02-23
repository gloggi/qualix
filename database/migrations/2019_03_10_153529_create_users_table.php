<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('kurs_id');
			$table->string('username', 30);
			$table->string('abteilung', 256);
			$table->char('password', 128);
			$table->string('email', 50);
			$table->char('salt', 128);
			$table->string('bild_url');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('users');
	}

}
