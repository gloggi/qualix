<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKursTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('kurs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 256);
			$table->string('kursnummer', 256);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('kurs');
	}

}
