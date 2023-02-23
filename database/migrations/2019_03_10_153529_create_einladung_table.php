<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEinladungTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('einladung', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('email', 50)->unique('email');
			$table->integer('kurs_id')->index('fk_kurs_einladung');
			$table->char('token', 128)->unique('token');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('einladung');
	}

}
