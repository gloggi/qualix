<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeiterTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('leiter', function(Blueprint $table)
		{
			$table->integer('kurs_id');
			$table->integer('user_id')->index('fk_user_kurs');
			$table->primary(['kurs_id','user_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('leiter');
	}

}
